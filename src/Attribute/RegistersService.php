<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use Attribute;
use Psr\Container\ContainerInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class RegistersService
{
    /** @var string[] */
    private array $aliases;
    private string $description;
    /** @var class-string */
    private string $type;

    /** @var string */
    public const DEFAULT_DESCRIPTION = 'No description available.';

    /**
     * @param class-string $type
     * @param string|string[] $aliases
     * @param string $description
     */
    public function __construct(string $type, string|array $aliases = [], string $description = self::DEFAULT_DESCRIPTION)
    {
        $this->aliases = (array) $aliases;
        $this->description = $description;
        $this->type = $type;
    }

    /** @return string[] */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /** @return class-string */
    public function getType(): string
    {
        return $this->type;
    }

    public function resolve(ContainerInterface $container): object
    {
        $service = $container->get($this->type);
        assert(is_object($service));

        return $service;
    }

    public function toArray(): array
    {
        return [
            'aliases' => $this->aliases,
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
