<?php
namespace Concrete\Core\Attribute\Key;

/*
 * Factory class for creating instances of the Attribute key category entity.
 * Class Category
 * @package Concrete\Core\Attribute\Key
 */
use Concrete\Core\Support\Facade\Facade;

class Category extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Category\CategoryFactory';
    }
}
