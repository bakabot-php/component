<?php

declare(strict_types = 1);

namespace Bakabot\Component;

interface DependentComponentInterface
{
    /**
     * @return array<int, class-string<ComponentInterface>>
     */
    public function getComponentDependencies(): array;
}
