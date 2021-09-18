<?php

declare(strict_types = 1);

namespace Bakabot\Component\Documentation;

use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;
use Bakabot\Component\Component;
use ReflectionClass;

final class Parser
{
    /**
     * @return RegistersParameter[]
     */
    public static function parseParameters(Component $component): array
    {
        $attributes = (new ReflectionClass($component))->getAttributes(RegistersParameter::class);

        $registeredParameters = [];
        foreach ($attributes as $attribute) {
            $registeredParameters[] = $attribute->newInstance();
        }

        return $registeredParameters;
    }

    /**
     * @return RegistersService[]
     */
    public static function parseServices(Component $component): array
    {
        $attributes = (new ReflectionClass($component))->getAttributes(RegistersService::class);

        $registeredServices = [];
        foreach ($attributes as $attribute) {
            $registeredServices[] = $attribute->newInstance();
        }

        return $registeredServices;
    }
}
