<?php

declare(strict_types = 1);

namespace Bakabot\Component;

class BlankDummyTest extends ComponentTestCase
{
    protected function component(): Component
    {
        return new BlankDummy();
    }
}
