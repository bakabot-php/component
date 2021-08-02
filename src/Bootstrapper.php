<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Acclimate\Container\ArrayContainer;
use Acclimate\Container\CompositeContainer;
use Bakabot\Component\Collection as ComponentCollection;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

final class Bootstrapper
{
    private ComponentCollection $components;
    private ?CompositeContainer $container = null;
    private ContainerInterface $wrappedContainer;

    /**
     * @param ComponentCollection|ComponentInterface[] $components
     * @param ContainerInterface|null $wrappedContainer
     */
    public function __construct(ComponentCollection|array $components, ?ContainerInterface $wrappedContainer = null)
    {
        if (is_array($components)) {
            $components = new ComponentCollection($components);
        }

        $this->components = $components;
        $this->wrappedContainer = $wrappedContainer ?? new ArrayContainer();
    }

    public function __destruct()
    {
        $this->shutdown();
        unset($this->container, $this->wrappedContainer);
    }

    public function boot(): ContainerInterface
    {
        if ($this->container !== null) {
            return $this->container;
        }

        $container = new CompositeContainer();
        $container->addContainer($this->wrappedContainer);

        $containerBuilder = $container->has(ContainerBuilder::class)
            ? $container->get(ContainerBuilder::class)
            : new ContainerBuilder()
        ;

        $containerBuilder->wrapContainer($container);

        foreach ($this->components as $component) {
            $component->register($containerBuilder);
        }

       $container->addContainer($containerBuilder->build());

        foreach ($this->components as $component) {
            $component->boot($container);
        }

        return $this->container = $container;
    }

    public function getComponents(): ComponentCollection
    {
        return $this->components;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->boot();
    }

    public function shutdown(): void
    {
        if ($this->container === null) {
            return;
        }

        foreach ($this->components as $component) {
            $component->shutdown($this->container);
        }

        $this->container = null;
    }
}
