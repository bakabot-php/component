<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Psr\Log\LoggerInterface;
use stdClass;

/**
 * @internal
 */
final class DependencyDummyTest extends ComponentTestCase
{
    protected function component(): Component
    {
        return new DependencyDummy();
    }

    /**
     * @test
     */
    public function registers_services_and_parameters(): void
    {
        $container = $this->container();

        self::assertSame($container->get('debug'), $this->hasDebugFlag());
        self::assertSame($container->get('base_dir'), $this->appDir());
        self::assertSame($container->get('env'), $this->appEnvironment());
        self::assertTrue($container->has('name'));
        self::assertTrue($container->has(LoggerInterface::class));
        self::assertTrue($container->has(stdClass::class));
    }
}
