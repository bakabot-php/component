<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

abstract class AbstractComponent implements ComponentInterface
{
    final public function __construct()
    {
    }

    public function boot(ContainerInterface $container): void
    {
    }

    abstract protected function getParameters(): array;

    abstract protected function getServices(): array;

    public function provideDependencies(ContainerInterface $container): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->wrapContainer($container);
        $containerBuilder->addDefinitions($this->getParameters(), $this->getServices());

        return $containerBuilder->build();
    }

    public function shutdown(ContainerInterface $container): void
    {
    }

    final public function __toString(): string
    {
        return static::class;
    }
}
