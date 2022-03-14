<?php

namespace Concrete\TestHelpers\Validation;

class NonStringableClass
{
    public function __toString()
    {
        return __CLASS__;
    }
}
