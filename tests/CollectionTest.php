<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /** @test */
    public function fresh_collection_contains_core_component(): void
    {
        $collection = new Collection();

        self::assertSame(1, iterator_count($collection));
    }

    /** @test */
    public function instantiating_with_basic_component_returns_it_during_iteration(): void
    {
        $component = new DependencyDummy();
        $components = [new CoreComponent(), $component];
        $collection = new Collection($components);

        self::assertEquals($components, iterator_to_array($collection));
    }

    /** @test */
    public function registering_basic_component_returns_it_during_iteration(): void
    {
        $component = new DependencyDummy();

        $collection = new Collection();
        $collection->push($component);

        self::assertEquals([new CoreComponent(), $component], iterator_to_array($collection));
    }

    /** @test */
    public function deduplicates_during_push(): void
    {
        $component = new DependencyDummy();

        $collection = new Collection();
        $collection->push($component);
        $collection->push($component);

        self::assertEquals([new CoreComponent(), $component], iterator_to_array($collection));
    }

    /** @test */
    public function registering_dependent_component_also_registers_dependencies(): void
    {
        $dependency = new DependencyDummy();
        $dependent = new DependentDummy();

        $collection = new Collection();
        $collection->push($dependent);

        self::assertEquals([new CoreComponent(), $dependency, $dependent], iterator_to_array($collection));
    }
}
