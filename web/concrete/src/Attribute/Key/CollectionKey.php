<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Support\Facade\Facade;

class CollectionKey extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Category\PageCategory';
    }

    public static function getByHandle($handle)
    {
        return static::getFacadeRoot()->getAttributeKeyByHandle($handle);
    }

    public static function getByID($akID)
    {
        return static::getFacadeRoot()->getAttributeKeyByID($akID);
    }

    /**
     * We need this here for old package installers that call CollectionKey::add()
     * @deprecated
     */
    public static function add($type, $args, $pkg = false)
    {
        $category = static::getFacadeApplication()->make('Concrete\Core\Attribute\Category\CategoryService')
            ->getByHandle('collection');
        return $category->getController()->add($type, $args, $pkg);
    }

}
