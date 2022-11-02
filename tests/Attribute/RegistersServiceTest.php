<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
final class RegistersServiceTest extends TestCase
{
    /**
     * @test
     */
    public function acts_as_dto(): void
    {
        $attr = new RegistersService(stdClass::class);

        $asArray = [
            'description' => $attr->description,
            'name' => $attr->name,
            'type' => $attr->type,
        ];

        self::assertSame($asArray, $attr->toArray());
    }
}
