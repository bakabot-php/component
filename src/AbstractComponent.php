<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use ReflectionObject;

abstract class AbstractComponent implements ComponentInterface
{
    final public function __construct()
    {
    }

    abstract protected function getParameters(): array;

    abstract protected function getServices(): array;

    public function boot(ContainerInterface $container): void
    {
    }

    /** @return RegistersParameter[] */
    final public function getRegisteredParameters(): array
    {
        $attributes = (new ReflectionObject($this))->getAttributes(RegistersParameter::class);

        $registeredParameters = [];
        foreach ($attributes as $attribute) {
            $registeredParameters[] = $attribute->newInstance();
        }

        return $registeredParameters;
    }

    /** @return RegistersService[] */
    final public function getRegisteredServices(): array
    {
        $attributes = (new ReflectionObject($this))->getAttributes(RegistersService::class);

        $registeredServices = [];
        foreach ($attributes as $attribute) {
            $registeredServices[] = $attribute->newInstance();
        }

        return $registeredServices;
    }

    public function register(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions($this->getParameters(), $this->getServices());
    }

    public function shutdown(ContainerInterface $container): void
    {
    }

    final public function __toString(): string
    {
        return static::class;
    }
}
