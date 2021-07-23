<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Acclimate\Container\ArrayContainer;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{
    /** @test */
    public function registers_services_and_parameters(): void
    {
        $component = new DependencyDummy();
        $container = $component->provideDependencies(new ArrayContainer());

        self::assertTrue($container->has('name'));
        self::assertTrue($container->has('my_service'));
    }
}
