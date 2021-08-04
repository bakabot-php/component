<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use Attribute;
use Psr\Container\ContainerInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class RegistersService
{
    private string $description;
    private string $name;
    /** @var class-string */
    private string $type;

    /** @var string */
    public const DEFAULT_DESCRIPTION = 'No description available.';

    /**
     * @param class-string $type
     * @param string|null $name
     * @param string $description
     */
    public function __construct(string $type, ?string $name = null, string $description = self::DEFAULT_DESCRIPTION)
    {
        $this->description = $description;
        $this->name = $name ?? $type;
        $this->type = $type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return class-string */
    public function getType(): string
    {
        return $this->type;
    }

    public function resolve(ContainerInterface $container): object
    {
        return $container->get($this->name);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
