<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use hanneskod\classtools\Iterator\ClassIterator;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder as SymfonyFinder;

final class Finder
{
    public const SEARCH_FOLDERS = '{src,vendor/bakabot}';

    /**
     * @return Component[]
     * @throws ReflectionException
     */
    public static function getInstances(SymfonyFinder $finder): array
    {
        $iterator = new ClassIterator($finder);
        $iterator->enableAutoloading();

        $components = [];

        /** @var ReflectionClass $class */
        foreach ($iterator->type(Component::class) as $class) {
            if ($class->isInstantiable() === false) {
                continue;
            }

            /** @var Component $component */
            $component = $class->newInstance();

            $components[] = $component;
        }

        return $components;
    }
}
