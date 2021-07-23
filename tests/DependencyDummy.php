<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use stdClass;

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
            'my_service' => static fn () => new stdClass(),
        ];
    }
}
