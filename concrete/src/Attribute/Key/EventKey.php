<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Support\Facade\Facade;

class EventKey extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Category\EventCategory';
    }

    public static function getByHandle($handle)
    {
        return static::getFacadeRoot()->getAttributeKeyByHandle($handle);
    }

    public static function getByID($akID)
    {
        return static::getFacadeRoot()->getAttributeKeyByID($akID);
    }

}
