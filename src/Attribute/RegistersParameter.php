<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use Attribute;
use Psr\Container\ContainerInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class RegistersParameter
{
    private mixed $defaultValue;
    private string $description;
    private string $name;
    private string $type;

    /** @var string */
    public const DEFAULT_DESCRIPTION = 'No description available.';
    /** @var string */
    public const DEFAULT_VALUE = '__BAKABOT_DEFAULT_VALUE_NONE';

    public function __construct(
        string $name,
        string $type,
        string $description = self::DEFAULT_DESCRIPTION,
        mixed $defaultValue = self::DEFAULT_VALUE
    ) {
        $this->defaultValue = $defaultValue;
        $this->description = $description;
        $this->name = $name;
        $this->type = $type;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function resolve(ContainerInterface $container): mixed
    {
        return $container->get($this->name);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'default_value' => $this->defaultValue,
            'description' => $this->description,
        ];
    }
}
