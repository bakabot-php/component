<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;

use function DI\decorate;

use Psr\Container\ContainerInterface;

use stdClass;

#[RegistersParameter('greeting', 'string', 'Hello World')]
#[RegistersService(stdClass::class, 'Adds a test flag to the instance', stdClass::class)]
final class DependentDummy extends AbstractComponent implements DependentComponent
{
    protected function parameters(): array
    {
        return [
            'greeting' => static fn (ContainerInterface $container) => sprintf('Hello %s', $container->get('name')),
        ];
    }

    protected function services(): array
    {
        return [
            stdClass::class => decorate(function (stdClass $previous) {
                $previous->test = true;

                return $previous;
            }),
        ];
    }

    public function dependencies(): array
    {
        return [
            DependencyDummy::class,
        ];
    }
}
