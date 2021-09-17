<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

abstract class AbstractComponent implements Component
{
    final public function __construct()
    {
    }

    abstract protected function parameters(): array;

    abstract protected function services(): array;

    public function boot(ContainerInterface $container): void
    {
    }

    public function register(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions($this->parameters(), $this->services());
    }

    public function shutdown(ContainerInterface $container): void
    {
    }

    final public function __toString(): string
    {
        return static::class;
    }
}
