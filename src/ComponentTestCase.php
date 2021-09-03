<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Bakabot\Component\Documentation\Parser;
use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

abstract class ComponentTestCase extends TestCase
{
    protected function setUp(): void
    {
        putenv('APP_DEBUG=true');
        putenv('APP_DIR=/tmp');
        putenv('APP_ENV=test');
    }

    final protected function assertContainerHasEntry(string $name, string $message = ''): void
    {
        self::assertTrue($this->getContainer()->has($name), $message);
    }

    final protected function getAppDebug(): bool
    {
        return (bool) getenv('APP_DEBUG');
    }

    final protected function getAppDir(): string
    {
        return getenv('APP_DIR') ?: '/tmp';
    }

    final protected function getAppEnvironment(): string
    {
        return getenv('APP_ENV') ?: 'test';
    }

    abstract protected function getComponent(): Component;

    /** @return Component[] */
    final protected function getComponents(): array
    {
        $component = $this->getComponent();

        $components = $component instanceof DependentComponent
            ? array_map(
                static fn (string $component) => new $component(),
                $component->getDependencies()
            )
            : []
        ;
        $components[] = $component;

        return $components;
    }

    final protected function getContainer(): Container
    {
        return $this->getContainerBuilder()->build();
    }

    final protected function getContainerBuilder(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        foreach ($this->getComponents() as $component) {
            $component->register($containerBuilder);
        }

        return $containerBuilder;
    }

    /** @test */
    public function registers_annotated_parameters(): void
    {
        $component = $this->getComponent();
        $parameters = Parser::parseParameters($component);

        if ($parameters === []) {
            /** @psalm-suppress InternalMethod */
            $this->addToAssertionCount(1);
            return;
        }

        $container = $this->getContainer();

        foreach ($parameters as $parameter) {
            $this->assertContainerHasEntry($parameter->name);

            self::assertSame(
                $parameter->type,
                get_debug_type($parameter->resolve($container)),
            );
        }
    }

    /** @test */
    public function registers_annotated_services(): void
    {
        $component = $this->getComponent();
        $services = Parser::parseServices($component);

        if ($services === []) {
            /** @psalm-suppress InternalMethod */
            $this->addToAssertionCount(1);
            return;
        }

        $container = $this->getContainer();

        foreach ($services as $service) {
            $this->assertContainerHasEntry(
                $service->type,
                sprintf('[%s] is not registered in the container', $service->type)
            );

            self::assertInstanceOf($service->type, $service->resolve($container));
        }
    }
}
