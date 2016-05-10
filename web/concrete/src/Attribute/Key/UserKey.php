<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Support\Facade\Facade;

class UserKey extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Category\UserCategory';
    }

    public static function getByHandle($handle)
    {
        return static::getFacadeRoot()->getAttributeKeyByHandle($handle);
    }
}
