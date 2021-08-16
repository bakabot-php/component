<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use Attribute;
use Psr\Container\ContainerInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class ExtendsService
{
    private string $description;
    /** @var class-string */
    private string $type;

    /** @var string */
    public const DEFAULT_DESCRIPTION = 'No description available.';

    /**
     * @param class-string $type
     * @param string $description
     */
    public function __construct(string $type, string $description = self::DEFAULT_DESCRIPTION)
    {
        $this->description = $description;
        $this->type = $type;
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
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
