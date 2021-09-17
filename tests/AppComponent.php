<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Bakabot\Component\Attribute\RegistersParameter;

use function DI\env;

#[RegistersParameter('env', 'string', "e:getenv('APP_ENV')")]
#[RegistersParameter('heck', 'string')]
class AppComponent extends AbstractComponent implements DependentComponent
{
    protected function parameters(): array
    {
        return [
            'env' => env('APP_ENV', 'prod'),
            'heck' => 'yeah',
        ];
    }

    protected function services(): array
    {
        return [];
    }

    public function dependencies(): array
    {
        return [DependentDummy::class];
    }
}
