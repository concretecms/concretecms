<?php
namespace Concrete\Core\Support\Facade;

class Session extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'session';
    }
}
