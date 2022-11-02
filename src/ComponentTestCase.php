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

    final protected function appDir(): string
    {
        return getenv('APP_DIR') ?: '/tmp';
    }

    final protected function appEnvironment(): string
    {
        return getenv('APP_ENV') ?: 'test';
    }

    final protected function assertContainerHasEntry(string $name, string $message = ''): void
    {
        self::assertTrue($this->container()->has($name), $message);
    }

    abstract protected function component(): Component;

    /**
     * @return Component[]
     */
    final protected function components(): array
    {
        $component = $this->component();

        $components = (
            $component instanceof DependentComponent
            ? array_map(
                static fn (string $component) => new $component(),
                $component->dependencies(),
            )
            : []
        );
        $components[] = $component;

        return $components;
    }

    final protected function container(): Container
    {
        return $this->containerBuilder()->build();
    }

    final protected function containerBuilder(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        foreach ($this->components() as $component) {
            $component->register($containerBuilder);
        }

        return $containerBuilder;
    }

    final protected function hasDebugFlag(): bool
    {
        return (bool) getenv('APP_DEBUG');
    }

    /**
     * @test
     * @psalm-suppress InternalMethod
     */
    public function registers_annotated_parameters(): void
    {
        $component = $this->component();
        $parameters = Parser::parameters($component);

        if ($parameters === []) {
            $this->addToAssertionCount(1);

            return;
        }

        $container = $this->container();

        foreach ($parameters as $parameter) {
            $this->assertContainerHasEntry(
                $parameter->name,
                sprintf('[%s] is not registered in the container', $parameter->name),
            );

            self::assertSame(
                $parameter->type,
                get_debug_type($parameter->resolve($container)),
                sprintf('[%s] is not of the expected type %s', $parameter->name, $parameter->type),
            );
        }
    }

    /**
     * @test
     * @psalm-suppress InternalMethod
     */
    public function registers_annotated_services(): void
    {
        $component = $this->component();
        $services = Parser::services($component);

        if ($services === []) {
            $this->addToAssertionCount(1);

            return;
        }

        $container = $this->container();

        foreach ($services as $service) {
            $this->assertContainerHasEntry(
                $service->type,
                sprintf('[%s] is not registered in the container', $service->type),
            );

            /* @noinspection UnnecessaryAssertionInspection */
            self::assertInstanceOf(
                $service->type,
                $service->resolve($container),
            );
        }
    }
}
