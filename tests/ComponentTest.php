<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;

class ComponentTest extends TestCase
{
    /** @test */
    public function registers_services_and_parameters(): void
    {
        $component = new DependencyDummy();

        $containerBuilder = new ContainerBuilder();
        $component->register($containerBuilder);

        $container = $containerBuilder->build();

        self::assertTrue($container->has('name'));
        self::assertTrue($container->has(stdClass::class));
    }
}
