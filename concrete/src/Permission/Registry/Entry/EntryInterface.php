<?php
namespace Concrete\Core\Permission\Registry\Entry;

interface EntryInterface
{

    function getAccessEntity();
    function getPermissionKeyHandles();

}
