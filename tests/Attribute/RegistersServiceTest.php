<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use PHPUnit\Framework\TestCase;
use stdClass;

class RegistersServiceTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $attr = new RegistersService(stdClass::class);

        $asArray = [
            'description' => $attr->getDescription(),
            'name' => $attr->getName(),
            'type' => $attr->getType(),
        ];

        self::assertSame($asArray, $attr->toArray());
    }
}
