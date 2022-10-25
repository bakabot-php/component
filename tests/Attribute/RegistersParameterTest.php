<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use PHPUnit\Framework\TestCase;

class RegistersParameterTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $attr = new RegistersParameter('param', 'string');

        $asArray = [
            'name' => $attr->name,
            'type' => $attr->type,
            'default' => $attr->default,
            'description' => $attr->description,
        ];

        self::assertSame($asArray, $attr->toArray());
    }
}
