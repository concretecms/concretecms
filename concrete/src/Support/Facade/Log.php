<?php
namespace Concrete\Core\Support\Facade;

class Log extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'log/factory';
    }

    /**
     * @deprecated
     */
    public static function addEntry($entry)
    {
        static::debug($entry);
    }
}
