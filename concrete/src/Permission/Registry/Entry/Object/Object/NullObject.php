<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

class NullObject implements ObjectInterface
{

    public function getPermissionObject()
    {
        return null;
    }

}
