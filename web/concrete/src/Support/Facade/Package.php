<?php
namespace Concrete\Core\Support\Facade;

class Package extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Package\PackageService';
    }

}
