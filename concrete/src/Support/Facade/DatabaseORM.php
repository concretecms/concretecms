<?php
namespace Concrete\Core\Support\Facade;

class DatabaseORM extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'database/orm';
    }

}
