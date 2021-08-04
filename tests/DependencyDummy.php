<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;
use stdClass;

#[RegistersParameter('name', 'string', 'World')]
#[RegistersService(stdClass::class)]
class DependencyDummy extends AbstractComponent
{
    protected function getParameters(): array
    {
        return [
            'name' => 'World',
        ];
    }

    protected function getServices(): array
    {
        return [
            stdClass::class => static fn () => new stdClass(),
        ];
    }
}
