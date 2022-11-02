<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

final class BlankDummy implements Component
{
    public function __construct()
    {
    }

    public function boot(ContainerInterface $container): void
    {
    }

    public function register(ContainerBuilder $containerBuilder): void
    {
    }

    public function shutdown(ContainerInterface $container): void
    {
    }

    public function __toString(): string
    {
        return 'blank';
    }
}
