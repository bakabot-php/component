<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class FinderTest extends TestCase
{
    /** @test */
    public function returns_empty_collection_in_invalid_dir(): void
    {
        $componentFinder = new Finder();

        $components = $componentFinder->collect();

        self::assertCount(1, $components);
    }

    /** @test */
    public function can_create_instances_of_dummies(): void
    {
        $symfonyFinder = new SymfonyFinder();
        $symfonyFinder->in('{' . dirname(__DIR__) . '/src' . ',' . __DIR__ . '}');

        $componentFinder = new Finder($symfonyFinder);

        $components = $componentFinder->collect();

        self::assertTrue($components->has(DependencyDummy::class));
        self::assertTrue($components->has(DependentDummy::class));
    }
}
