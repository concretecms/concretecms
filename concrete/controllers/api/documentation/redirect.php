<?php

namespace Concrete\Controller\Api\Documentation;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Api\Documentation\RedirectUriFactory;
use Concrete\Core\Entity\OAuth\ClientRepository;
use Concrete\Core\Permission\Checker;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class Redirect extends BackendInterfaceController
{
    protected $viewPath = '/api/documentation/redirect';

    /**
     * @return bool
     */
    protected function canAccess()
    {
        $checker = new Checker();
        return $checker->canAccessApi(); // `access_api` custom task permission
    }

    public function view($clientId)
    {
        /**
         * @var $clientRepository ClientRepository
         */
        $clientRepository = $this->app->make(ClientRepositoryInterface::class);
        $client = $clientRepository->findOneByIdentifier($clientId);
        if (!$client) {
            throw new \Exception(t('Invalid API client.'));
        }
    }


}
