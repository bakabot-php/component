<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

abstract class ComponentTestCase extends TestCase
{
    final protected function assertContainerHasEntry(string $name, string $message = ''): void
    {
        self::assertTrue($this->getContainer()->has($name), $message);
    }

    abstract protected function getComponent(): ComponentInterface;

    /** @return ComponentInterface[] */
    final protected function getComponents(): array
    {
        $component = $this->getComponent();

        $components = $component instanceof DependentComponentInterface
            ? array_map(
                static fn (string $component) => new $component(),
                $component->getComponentDependencies()
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

        if (($component instanceof AbstractComponent) === false || $component->getRegisteredParameters() === []) {
            /** @psalm-suppress InternalMethod */
            $this->addToAssertionCount(1);
            return;
        }

        $container = $this->getContainer();

        foreach ($component->getRegisteredParameters() as $parameter) {
            $this->assertContainerHasEntry($parameter->getName());

            self::assertSame(
                $parameter->getType(),
                get_debug_type($parameter->resolve($container)),
            );
        }
    }

    /** @test */
    public function registers_annotated_services(): void
    {
        $component = $this->getComponent();

        if (($component instanceof AbstractComponent) === false || $component->getRegisteredServices() === []) {
            /** @psalm-suppress InternalMethod */
            $this->addToAssertionCount(1);
            return;
        }

        $container = $this->getContainer();

        foreach ($component->getRegisteredServices() as $service) {
            $this->assertContainerHasEntry(
                $service->getType(),
                sprintf('[%s] is not registered in the container', $service->getType())
            );

            foreach ($service->getAliases() as $alias) {
                $this->assertContainerHasEntry($alias, "[$alias] is not registered in the container");
            }

            self::assertInstanceOf(
                $service->getType(),
                $service->resolve($container)
            );
        }
    }
}
