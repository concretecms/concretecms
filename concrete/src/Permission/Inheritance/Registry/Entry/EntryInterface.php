<?php
namespace Concrete\Core\Permission\Inheritance\Registry\Entry;

interface EntryInterface
{

    function getInheritedFromPermissionKeyCategoryHandle();
    function getPermissionKeyHandle();
    function getInheritedFromPermissionKeyHandle();

}
