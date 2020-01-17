<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\Category\SiteTypeCategory;
use Concrete\Core\Support\Facade\Facade;

class SiteTypeKey extends Facade
{
    public static function getFacadeAccessor()
    {
        return SiteTypeCategory::class;
    }

}
