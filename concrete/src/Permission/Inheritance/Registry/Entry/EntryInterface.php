<?php
namespace Concrete\Core\Permission\Inheritance\Registry\Entry;

/**
 * @since 8.0.0
 */
interface EntryInterface
{

    function getInheritedFromPermissionKeyCategoryHandle();
    function getPermissionKeyHandle();
    function getInheritedFromPermissionKeyHandle();

}
