<?php
namespace Concrete\Core\Permission\Registry;

interface EntryInterface
{

    function getAccessEntity();
    function getPermissionKeyHandles();

}
