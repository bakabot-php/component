<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Acclimate\Container\ArrayContainer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class BootstrapperTest extends TestCase
{
    /** @test */
    public function allows_access_to_components(): void
    {
        $component = $this->createMock(ComponentInterface::class);
        $components = [$component];
        $bootstrapper = new Bootstrapper($components);

        self::assertEquals(new Collection($components), $bootstrapper->getComponents());
    }

    /** @test */
    public function produced_container_has_access_to_parent_dependencies(): void
    {
        $components = [new DependentDummy()];
        $bootstrapper = new Bootstrapper($components);

        $container = $bootstrapper->getContainer();

        self::assertTrue($container->has('name'));
        self::assertSame('World', $container->get('name'));

        self::assertTrue($container->has('greeting'));
        self::assertSame('Hello World', $container->get('greeting'));
    }

    /** @test */
    public function produced_container_can_optionally_wrap_a_top_level_container(): void
    {
        $wrappedContainer = new ArrayContainer();
        $wrappedContainer[LoggerInterface::class] = static fn () => new NullLogger();

        $components = [new DependentDummy()];
        $bootstrapper = new Bootstrapper($components, $wrappedContainer);

        $container = $bootstrapper->getContainer();

        self::assertTrue($container->has(LoggerInterface::class));
        self::assertInstanceOf(NullLogger::class, $container->get(LoggerInterface::class));
    }

    /** @test */
    public function components_are_booted_and_shutdown_only_once(): void
    {
        $component = $this->createMock(ComponentInterface::class);
        $component
            ->expects($this->once())
            ->method('boot');

        $component
            ->expects($this->once())
            ->method('shutdown');

        $bootstrapper = new Bootstrapper([$component]);

        self::assertSame($bootstrapper->boot(), $bootstrapper->getContainer());

        $bootstrapper->shutdown();
        $bootstrapper->shutdown();
    }
}
