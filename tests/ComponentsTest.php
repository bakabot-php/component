<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ComponentsTest extends TestCase
{
    /**
     * @test
     */
    public function deduplicates_during_push(): void
    {
        $component = new DependencyDummy();

        $collection = new Components();
        $collection->add($component);
        $collection->add($component);

        self::assertEquals([$component], iterator_to_array($collection));
    }

    /**
     * @test
     */
    public function instantiating_with_basic_component_returns_it_during_iteration(): void
    {
        $component = new DependencyDummy();
        $components = new Components($component);

        self::assertTrue($components->has($component));
    }

    /**
     * @test
     */
    public function registering_basic_component_returns_it_during_iteration(): void
    {
        $component = new DependencyDummy();

        $components = new Components();
        $components->add($component);

        self::assertTrue($components->has($component));
    }

    /**
     * @test
     */
    public function registering_dependent_component_also_registers_dependencies(): void
    {
        $dependency = new DependencyDummy();
        $dependent = new DependentDummy();

        $collection = new Components();
        $collection->add($dependent);

        self::assertEquals([$dependency, $dependent], iterator_to_array($collection));
    }
}
