<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Support\Facade\Facade;

class Key extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Key\Factory';
    }

}
