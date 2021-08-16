<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Bakabot\Component\Attribute\RegistersParameter;

use function DI\env;

#[RegistersParameter('env', 'string', "e:getenv('APP_ENV')")]
#[RegistersParameter('heck', 'string')]
class AppComponent extends AbstractComponent implements DependentComponent
{
    protected function getParameters(): array
    {
        return [
            'env' => env('APP_ENV', 'prod'),
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
