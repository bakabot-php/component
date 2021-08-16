<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Bakabot\Component\Attribute\RegistersParameter;

#[RegistersParameter('heck', 'string')]
class AppComponent extends AbstractComponent implements DependentComponent
{
    protected function getParameters(): array
    {
        return [
            'heck' => 'yeah',
        ];
    }

    protected function getServices(): array
    {
        return [];
    }

    public function getDependencies(): array
    {
        return [DependentDummy::class];
    }
}
