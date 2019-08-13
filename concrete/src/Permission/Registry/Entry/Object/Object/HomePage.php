<?php

namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Page\Page as ConcretePage;

/**
 * @since 8.0.0
 */
class HomePage implements ObjectInterface
{
    public function getPermissionObject()
    {
        return ConcretePage::getByID(ConcretePage::getHomePageID());
    }
}
