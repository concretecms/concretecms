<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Validation\CSRF\Token;

class Versions extends BackendInterfaceFileController
{
    protected $viewPath = '/dialogs/file/versions';

    protected function canAccess()
    {
        return $this->permissions->canEditFileProperties();
    }

    public function view()
    {
        $fv = $this->file->getApprovedVersion();
        $this->set('dh', new Date());
        $this->set('token', new Token());
        $this->set('fv', $fv);
    }

}
