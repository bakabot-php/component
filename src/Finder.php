<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use hanneskod\classtools\Iterator\ClassIterator;
use ReflectionClass;
use Symfony\Component\Finder\Finder as SymfonyFinder;

final class Finder
{
    private SymfonyFinder $finder;

    public const SEARCH_FOLDERS = '{src,vendor/bakabot}';

    public function __construct(SymfonyFinder $finder = null)
    {
        if ($finder === null) {
            $finder = new SymfonyFinder();
            $finder->in(self::SEARCH_FOLDERS);
        }

        $this->finder = $finder;
    }

    public function collect(): Components
    {
        $iterator = new ClassIterator($this->finder);
        $iterator->enableAutoloading();

        $components = new Components();

        /** @var ReflectionClass<Component> $class */
        foreach ($iterator->type(Component::class) as $class) {
            if ($class->isInstantiable() === false) {
                continue;
            }

            $components->add($class->newInstance());
        }

        return $components;
    }
}
