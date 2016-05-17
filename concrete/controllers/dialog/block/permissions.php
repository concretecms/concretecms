<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use View;

class Permissions extends BackendInterfaceBlockController
{
    public function viewList()
    {
        $v = new View('/dialogs/block/permissions/list');
        $this->view = $v;
    }

    public function viewDetail()
    {
        $v = new View('/dialogs/block/permissions/detail');
        $this->view = $v;
    }

    public function viewGuestAccess()
    {
        $v = new View('/dialogs/block/permissions/guest_access');
        $this->view = $v;
    }

    protected function canAccess()
    {
        return $this->permissions->canEditBlockPermissions();
    }
}
