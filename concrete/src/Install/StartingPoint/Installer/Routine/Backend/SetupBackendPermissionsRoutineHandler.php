<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Backend;

use Concrete\Core\Page\Page;
use Concrete\Core\User\Group\Group;

class SetupBackendPermissionsRoutineHandler
{

    public function __invoke()
    {
        $adminGroup = Group::getByID(ADMIN_GROUP_ID);
        $dashboard = Page::getByPath('/dashboard', 'RECENT');
        $dashboard->assignPermissions($adminGroup, ['view_page']);
    }


}
