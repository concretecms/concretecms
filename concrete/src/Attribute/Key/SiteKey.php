<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Support\Facade\Facade;

class SiteKey extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Category\SiteCategory';
    }

}
