<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\File\File;
use Concrete\Core\Permission\Key\Key;

class Properties extends BackendInterfaceController
{

    protected $viewPath = '/dialogs/file/properties';

    public function canAccess()
    {
        $file = File::getByID($this->request->attributes->get('fID'));
        $permissions = new Checker($file);
        return $permissions->canEditFileProperties();
    }

    public function view($fID)
    {
        $file = File::getByID($fID);
        $this->set('version', $file->getApprovedVersion());
    }
}


