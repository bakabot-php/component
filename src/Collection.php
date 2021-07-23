<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use ArrayIterator;
use IteratorAggregate;
use Stringable;
use Traversable;

final class Collection implements IteratorAggregate
{
    /** @var ComponentInterface[] */
    private array $components = [];

    /**
     * @param ComponentInterface[] $components
     */
    public function __construct(array $components = [])
    {
        foreach ($components as $component) {
            $this->push($component);
        }
    }

    private function registerComponentDependencies(ComponentInterface $component): void
    {
        if (!($component instanceof DependentComponentInterface)) {
            return;
        }

        foreach ($component->getComponentDependencies() as $dependency) {
            if (!$this->has($dependency)) {
                $this->push(new $dependency());
            }
        }
    }

    /**
     * @return ArrayIterator<array-key, ComponentInterface>
     */
    public function getIterator(): Traversable
    {
        reset($this->components);

        return new ArrayIterator(array_values($this->components));
    }

    /**
     * @param ComponentInterface|class-string<ComponentInterface> $component
     * @return bool
     */
    public function has(string|Stringable $component): bool
    {
        return isset($this->components[(string) $component]);
    }

    public function push(ComponentInterface $component): void
    {
        if ($this->has($component)) {
            return;
        }

        $this->registerComponentDependencies($component);
        $this->components[(string) $component] = $component;
    }
}
