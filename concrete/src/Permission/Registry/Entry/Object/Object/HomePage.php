<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;

class HomePage implements ObjectInterface
{


    public function getPermissionObject()
    {
        return \Concrete\Core\Page\Page::getByID(HOME_CID);
    }


}
