<?php

namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Page\Page;

class HomePage implements ObjectInterface
{
    public function getPermissionObject()
    {
        return Page::getByID(Page::getHomePageID());
    }
}
