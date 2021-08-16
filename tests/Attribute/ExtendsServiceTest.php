<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use PHPUnit\Framework\TestCase;
use stdClass;

class ExtendsServiceTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $attr = new ExtendsService(stdClass::class);

        $asArray = [
            'type' => $attr->getType(),
            'description' => $attr->getDescription(),
        ];

        self::assertSame($asArray, $attr->toArray());
    }
}
