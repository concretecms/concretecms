<?php
namespace Concrete\Core\Support\Facade;

/**
 * @since 8.0.0
 */
class Package extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Package\PackageService';
    }

}
