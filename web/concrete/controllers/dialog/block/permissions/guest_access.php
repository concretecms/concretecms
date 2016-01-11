<?php
namespace Concrete\Controller\Dialog\Block\Permissions;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;

class GuestAccess extends BackendInterfaceBlockController
{
    protected $viewPath = '/dialogs/block/permissions/guest_access';

    protected function canAccess()
    {
        return $this->permissions->canScheduleGuestAccess() && $this->permissions->canGuestsViewThisBlock();
    }
}
