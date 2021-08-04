<?php

declare(strict_types = 1);

namespace Bakabot\Component;

class BlankDummyComponentTest extends ComponentTestCase
{
    protected function getComponent(): ComponentInterface
    {
        return new BlankDummy();
    }
}
