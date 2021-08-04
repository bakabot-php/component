<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class BlankDummy implements ComponentInterface
{
    public function __construct()
    {
    }

    public function boot(ContainerInterface $container): void
    {
        // TODO: Implement boot() method.
    }

    public function register(ContainerBuilder $containerBuilder): void
    {
        // TODO: Implement register() method.
    }

    public function shutdown(ContainerInterface $container): void
    {
        // TODO: Implement shutdown() method.
    }

    public function __toString(): string
    {
        return 'blank';
    }
}
