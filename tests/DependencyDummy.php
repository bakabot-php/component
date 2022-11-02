<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;

use function DI\env;
use function DI\get;

use Psr\Log\LoggerInterface;

use Psr\Log\NullLogger;
use stdClass;

#[RegistersParameter('name', 'string', 'World')]
#[RegistersService('logger', '', LoggerInterface::class)]
#[RegistersService(stdClass::class)]
final class DependencyDummy extends AbstractComponent
{
    protected function parameters(): array
    {
        return [
            'base_dir' => env('APP_DIR'),
            'debug' => fn () => (bool) getenv('APP_DEBUG'),
            'env' => env('APP_ENV'),
            'name' => 'World',
        ];
    }

    protected function services(): array
    {
        return [
            'logger' => get(LoggerInterface::class),
            LoggerInterface::class => static fn () => new NullLogger(),
            stdClass::class => static fn () => new stdClass(),
        ];
    }
}
