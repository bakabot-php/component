<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use Attribute;
use Psr\Container\ContainerInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class RegistersParameter
{
    /**
     * @var string
     */
    private const DEFAULT_EMPTY_VALUE = '__BAKABOT_DEFAULT_VALUE_NONE';

    /**
     * @var string
     */
    public const DEFAULT_DESCRIPTION = 'No description available.';

    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly mixed $default = self::DEFAULT_EMPTY_VALUE,
        public readonly string $description = self::DEFAULT_DESCRIPTION,
    ) {
    }

    public function hasDefault(): bool
    {
        return $this->default !== self::DEFAULT_EMPTY_VALUE;
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
            'default' => $this->default,
            'description' => $this->description,
        ];
    }
}
