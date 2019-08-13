<?php
namespace Concrete\Core\Support\Facade;

/**
 * @since 5.7.4
 */
class DatabaseORM extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'database/orm';
    }
}
