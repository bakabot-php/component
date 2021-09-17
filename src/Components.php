<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

final class Components implements Countable, IteratorAggregate
{
    /** @var Component[] */
    private array $components = [];

    public function __construct(Component ...$components)
    {
        $this->add(new CoreComponent());

        foreach ($components as $component) {
            $this->add($component);
        }
    }

    private function registerDependencies(Component $component): void
    {
        if (
            !($component instanceof DependentComponent)
            || ($component instanceof CoreComponent)
        ) {
            return;
        }

        foreach ($component->dependencies() as $dependency) {
            $this->add(new $dependency());
        }
    }

    public function add(Component $component): void
    {
        if ($this->has($component)) {
            return;
        }

        $this->registerDependencies($component);
        $this->components[(string) $component] = $component;
    }

    public function count(): int
    {
        return count($this->components);
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
    public function has(string|Component $component): bool
    {
        return isset($this->components[(string) $component]);
    }
}
