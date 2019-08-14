<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Support\Facade\Facade;

class Set extends Facade
{
    /**
     * @since 8.0.0
     */
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\SetFactory';
    }

    /**
     * @deprecated
     * @since 5.7.3
     */
    public static function exportTranslations()
    {
        $factory = static::getFacadeRoot();
        $translations = $factory->exportTranslations();
        return $translations;
    }
}
