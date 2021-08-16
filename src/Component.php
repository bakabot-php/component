<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Stringable;

interface Component extends Stringable
{
    public function __construct();

    public function boot(ContainerInterface $container): void;

    public function register(ContainerBuilder $containerBuilder): void;

    public function shutdown(ContainerInterface $container): void;
}
