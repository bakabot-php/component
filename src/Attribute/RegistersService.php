<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use Attribute;
use Psr\Container\ContainerInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class RegistersService
{
    private string $description;
    /** @var class-string|string */
    private string $name;
    /** @var class-string */
    private string $type;

    /** @var string */
    public const DEFAULT_DESCRIPTION = 'No description available.';

    /**
     * @param class-string|string $name
     * @param class-string|null $type
     * @param string $description
     */
    public function __construct(string $name, ?string $type = null, string $description = self::DEFAULT_DESCRIPTION)
    {
        $this->description = $description;
        $this->name = $name;

        if ($type === null) {
            assert(class_exists($name));
            $type = $name;
        }

        $this->type = $type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /** @return class-string|string */
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
        $service = $container->get($this->name);
        assert(is_object($service));
        assert(is_a($service, $this->type));

        return $service;
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}
