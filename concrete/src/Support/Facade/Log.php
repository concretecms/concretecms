<?php
namespace Concrete\Core\Support\Facade;

class Log extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'log/application';
    }

    /**
     * @deprecated
     */
    public static function addEntry($entry)
    {
        static::debug($entry);
    }
}
