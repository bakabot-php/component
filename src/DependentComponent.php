<?php

declare(strict_types = 1);

namespace Bakabot\Component;

interface DependentComponent
{
    /**
     * @return array<int, class-string<Component>>
     */
    public function getDependencies(): array;
}
