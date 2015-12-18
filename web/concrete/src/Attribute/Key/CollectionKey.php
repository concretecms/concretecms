<?php

namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Support\Facade\Facade;

class CollectionKey extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Category\PageCategory';
    }

    public static function getByHandle($handle)
    {
        return static::getFacadeRoot()->getAttributeKeyByHandle($handle);
    }

}
