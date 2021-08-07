<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Psr\Log\LoggerInterface;
use stdClass;

class DependencyDummyComponentTest extends ComponentTestCase
{
    protected function getComponent(): ComponentInterface
    {
        return new DependencyDummy();
    }

    /** @test */
    public function registers_services_and_parameters(): void
    {
        $container = $this->getContainer();

        self::assertSame($container->get('debug'), $this->getAppDebug());
        self::assertSame($container->get('base_dir'), $this->getAppDir());
        self::assertSame($container->get('env'), $this->getAppEnvironment());
        self::assertTrue($container->has('name'));
        self::assertTrue($container->has(LoggerInterface::class));
        self::assertTrue($container->has(stdClass::class));
    }
}
