<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
final class AbstractComponentTest extends TestCase
{
    /**
     * @test
     */
    public function ffs_give_me_my_coverage(): void
    {
        $component = new class() extends AbstractComponent {
            private bool $wasBooted = false;

            private bool $wasShutdown = false;

            protected function parameters(): array
            {
                return [];
            }

            protected function services(): array
            {
                return [];
            }

            public function boot(ContainerInterface $container): void
            {
                parent::boot($container);

                $this->wasBooted = true;
            }

            public function shutdown(ContainerInterface $container): void
            {
                parent::shutdown($container);

                $this->wasShutdown = true;
            }

            public function wasBooted(): bool
            {
                return $this->wasBooted;
            }

            public function wasShutdown(): bool
            {
                return $this->wasShutdown;
            }
        };

        self::assertFalse($component->wasBooted());
        self::assertFalse($component->wasShutdown());

        $container = $this->createMock(ContainerInterface::class);
        $component->boot($container);
        self::assertTrue($component->wasBooted());

        $component->shutdown($container);
        self::assertTrue($component->wasShutdown());
    }
}
