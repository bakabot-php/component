<?php

declare(strict_types = 1);

namespace Bakabot\Component\Attribute;

use Attribute;
use Psr\Container\ContainerInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class RegistersService
{
    public /*readonly*/ string $description;
    /** @var class-string|string */
    public /*readonly*/ string $name;
    /** @var class-string */
    public /*readonly*/ string $type;

    /** @var string */
    public const DEFAULT_DESCRIPTION = 'No description available.';

    /**
     * @param class-string|string $name
     * @param string $description
     * @param class-string|null $type
     */
    public function __construct(
        string $name,
        string $description = self::DEFAULT_DESCRIPTION,
        ?string $type = null
    ) {
        $this->description = $description;
        $this->name = $name;

        if ($type === null) {
            assert(interface_exists($name) || class_exists($name));
            $type = $name;
        }

        $this->type = $type;
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
