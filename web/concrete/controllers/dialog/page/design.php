<?php
namespace Concrete\Controller\Dialog\Page;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;

class Design extends \Concrete\Controller\Panel\Page\Design
{

    protected $viewPath = '/dialogs/page/design';

    public function canAccess()
    {
        return $this->permissions->canEditPageType() || parent::canAccess();
    }
}