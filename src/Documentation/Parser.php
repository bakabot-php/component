<?php

declare(strict_types = 1);

namespace Bakabot\Component\Documentation;

use Bakabot\Component\Attribute\ExtendsService;
use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;
use Bakabot\Component\Component;
use ReflectionClass;
use ReflectionException;

final class Parser
{
    /**
     * @param class-string<Component>|Component $component
     * @return ExtendsService[]
     * @throws ReflectionException
     */
    public static function parseExtendedServices(string|Component $component): array
    {
        $attributes = (new ReflectionClass($component))->getAttributes(ExtendsService::class);

        $registeredServices = [];
        foreach ($attributes as $attribute) {
            $registeredServices[] = $attribute->newInstance();
        }

        return $registeredServices;
    }

    /**
     * @param class-string<Component>|Component $component
     * @return RegistersParameter[]
     * @throws ReflectionException
     */
    public static function parseParameters(string|Component $component): array
    {
        $attributes = (new ReflectionClass($component))->getAttributes(RegistersParameter::class);

        $registeredParameters = [];
        foreach ($attributes as $attribute) {
            $registeredParameters[] = $attribute->newInstance();
        }

        return $registeredParameters;
    }

    /**
     * @param class-string<Component>|Component $component
     * @return RegistersService[]
     * @throws ReflectionException
     */
    public static function parseServices(string|Component $component): array
    {
        $attributes = (new ReflectionClass($component))->getAttributes(RegistersService::class);

        $registeredServices = [];
        foreach ($attributes as $attribute) {
            $registeredServices[] = $attribute->newInstance();
        }

        return $registeredServices;
    }
}
