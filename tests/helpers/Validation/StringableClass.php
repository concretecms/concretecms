<?php

namespace Concrete\TestHelpers\Validation;

class StringableClass
{
    public function __toString()
    {
        return __CLASS__;
    }
}
