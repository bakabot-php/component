<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use stdClass;

class DependentDummyTest extends ComponentTestCase
{
    protected function component(): Component
    {
        return new DependentDummy();
    }

    /** @test */
    public function registers_services_and_parameters(): void
    {
        $container = $this->container();

        self::assertTrue($container->has('name'));
        self::assertTrue($container->has(stdClass::class));
    }
}
