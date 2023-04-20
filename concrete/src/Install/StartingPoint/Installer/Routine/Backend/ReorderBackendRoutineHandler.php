<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Backend;

use Concrete\Core\Page\Page;

class ReorderBackendRoutineHandler
{

    public function __invoke()
    {
        $desktop = Page::getByPath('/dashboard/welcome');
        $desktop->movePageDisplayOrderToTop();
    }


}
