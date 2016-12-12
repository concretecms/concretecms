<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Support\Facade\Facade;

class Type extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\TypeFactory';
    }

    /**
     * @deprecated
     */
    public static function exportTranslations()
    {
        $factory = static::getFacadeRoot();
        $translations = $factory->exportTranslations();
        return $translations;
    }

}
