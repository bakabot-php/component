<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Psr\Container\ContainerInterface;

class DependentDummy extends AbstractComponent implements DependentComponentInterface
{
    protected function getParameters(): array
    {
        return [
            'greeting' => static function (ContainerInterface $container) {
                return sprintf('Hello %s', $container->get('name'));
            }
        ];
    }

    protected function getServices(): array
    {
        return [];
    }

    public function getComponentDependencies(): array
    {
        return [
            DependencyDummy::class,
        ];
    }
}
