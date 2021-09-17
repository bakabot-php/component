<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use PHPUnit\Framework\TestCase;

class ComponentsTest extends TestCase
{
    /** @test */
    public function fresh_collection_contains_core_component(): void
    {
        $components = new Components();

        self::assertTrue($components->has(CoreComponent::class));
    }

    /** @test */
    public function instantiating_with_basic_component_returns_it_during_iteration(): void
    {
        $component = new DependencyDummy();
        $components = new Components($component);

        self::assertTrue($components->has(CoreComponent::class));
        self::assertTrue($components->has($component));
    }

    /** @test */
    public function registering_basic_component_returns_it_during_iteration(): void
    {
        $component = new DependencyDummy();

        $components = new Components();
        $components->add($component);

        self::assertTrue($components->has(CoreComponent::class));
        self::assertTrue($components->has($component));
    }

    /** @test */
    public function deduplicates_during_push(): void
    {
        $component = new DependencyDummy();

        $collection = new Components();
        $collection->add($component);
        $collection->add($component);

        self::assertEquals([new CoreComponent(), $component], iterator_to_array($collection));
    }

    /** @test */
    public function registering_dependent_component_also_registers_dependencies(): void
    {
        $dependency = new DependencyDummy();
        $dependent = new DependentDummy();

        $collection = new Components();
        $collection->add($dependent);

        self::assertEquals([new CoreComponent(), $dependency, $dependent], iterator_to_array($collection));
    }
}
