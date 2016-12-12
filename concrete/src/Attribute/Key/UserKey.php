<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Support\Facade\Facade;

class UserKey extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Category\UserCategory';
    }

    public static function getByHandle($handle)
    {
        return static::getFacadeRoot()->getAttributeKeyByHandle($handle);
    }

    /**
     * We need this here for old package installers that call UserKey::add()
     * @deprecated
     */
    public static function add($type, $args, $pkg = false)
    {
        $category = static::getFacadeApplication()->make('Concrete\Core\Attribute\Category\CategoryService')
            ->getByHandle('user');
        return $category->getController()->add($type, $args, $pkg);
    }
}
