<?php
namespace Concrete\Core\Support\Facade;

class Request extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'request';
    }

    public static function getInstance()
    {
        return static::getFacadeRoot();
    }

}
