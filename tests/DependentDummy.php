<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;
use Psr\Container\ContainerInterface;
use stdClass;
use function DI\decorate;

#[RegistersParameter('greeting', 'string', 'Hello World')]
#[RegistersService(stdClass::class, 'Adds a test flag to the instance')]
class DependentDummy extends AbstractComponent implements DependentComponent
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
        return [
            stdClass::class => decorate(function (stdClass $previous) {
                $previous->test = true;

                return $previous;
            }),
        ];
    }

    public function getDependencies(): array
    {
        return [
            DependencyDummy::class,
        ];
    }
}
