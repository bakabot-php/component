<?php

declare(strict_types = 1);

namespace Bakabot\Component;

class BlankDummyTest extends ComponentTestCase
{
    protected function getComponent(): Component
    {
        return new BlankDummy();
    }
}
