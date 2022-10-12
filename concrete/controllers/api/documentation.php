<?php

namespace Concrete\Controller\Api;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Api\Documentation\RedirectUriFactory;
use Concrete\Core\Permission\Checker;
use Concrete\Package\ConcreteApiProposal2022\Controller as ApiPackageController;

class Documentation extends BackendInterfaceController
{
    protected $viewPath = '/api/documentation';

    /**
     * @return bool
     */
    protected function canAccess()
    {
        $checker = new Checker();
        return $checker->canAccessApi(); // `access_api` custom task permission
    }

    public function view()
    {
        $this->set('oauth2RedirectUrl',
           $this->app->make(RedirectUriFactory::class)->createDocumentationRedirectUri()
        );
    }

}
