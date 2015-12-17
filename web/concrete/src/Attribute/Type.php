<?php

namespace Concrete\Core\Attribute;

use Concrete\Core\Support\Facade\Facade;

class Type extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\TypeFactory';
    }


}
