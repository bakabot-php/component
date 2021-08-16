<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class FinderTest extends TestCase
{
    /** @test */
    public function can_create_instances_of_dummies(): void
    {
        $symfonyFinder = new SymfonyFinder();
        $symfonyFinder->in('{' . dirname(__DIR__) . '/src' . ',' . __DIR__ . '}');

        $components = Finder::getInstances($symfonyFinder);

        self::assertCount(4, $components);
    }
}
