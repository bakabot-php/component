<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

abstract class ComponentTestCase extends TestCase
{
    abstract protected function getComponent(): ComponentInterface;

    /** @return ComponentInterface[] */
    final protected function getComponents(): array
    {
        $component = $this->getComponent();

        $components = $component instanceof DependentComponentInterface
            ? array_map(
                static fn (string $component) => new $component(),
                $component->getComponentDependencies()
            )
            : []
        ;
        $components[] = $component;

        return $components;
    }

    final protected function getContainer(): Container
    {
        return $this->getContainerBuilder()->build();
    }

    final protected function getContainerBuilder(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        foreach ($this->getComponents() as $component) {
            $component->register($containerBuilder);
        }

        return $containerBuilder;
    }
}
