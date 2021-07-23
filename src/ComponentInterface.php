<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Psr\Container\ContainerInterface;
use Stringable;

interface ComponentInterface extends Stringable
{
    public function __construct();

    public function boot(ContainerInterface $container): void;

    public function provideDependencies(ContainerInterface $container): ContainerInterface;

    public function shutdown(ContainerInterface $container): void;
}
