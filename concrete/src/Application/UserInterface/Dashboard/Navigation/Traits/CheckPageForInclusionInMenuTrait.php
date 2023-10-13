<?php

namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation\Traits;

use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

trait CheckPageForInclusionInMenuTrait
{

    public function includePageInMenu(Page $page): bool
    {
        $permissions = new Checker($page);
        if ($permissions->canViewPage() && !$page->getAttribute('exclude_nav')) {
            return true;
        }
        return false;
    }

}