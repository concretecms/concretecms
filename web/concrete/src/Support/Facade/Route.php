<?php
namespace Concrete\Core\Support\Facade;

class Route extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Routing\RouterInterface';
    }
}
