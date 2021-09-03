<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use ArrayIterator;
use IteratorAggregate;
use Stringable;
use Traversable;

final class Collection implements IteratorAggregate
{
    /** @var Component[] */
    private array $components = [];

    /**
     * @param Component[] $components
     */
    public function __construct(array $components = [])
    {
        foreach ($components as $component) {
            $this->push($component);
        }
    }

    private function registerDependencies(Component $component): void
    {
        if (!($component instanceof DependentComponent)) {
            return;
        }

        foreach ($component->getDependencies() as $dependency) {
            if (!$this->has($dependency)) {
                $this->push(new $dependency());
            }
        }
    }

    /**
     * @return ArrayIterator<array-key, Component>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator(array_values($this->components));
    }

    /**
     * @param Component|class-string<Component> $component
     * @return bool
     */
    public function has(string|Stringable $component): bool
    {
        return isset($this->components[(string) $component]);
    }

    public function push(Component $component): void
    {
        if ($this->has($component)) {
            return;
        }

        $this->registerDependencies($component);
        $this->components[(string) $component] = $component;
    }
}
