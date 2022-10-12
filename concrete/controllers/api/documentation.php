<?php

namespace Concrete\Controller\Api;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Api\Documentation\RedirectUriFactory;
use Concrete\Core\Entity\OAuth\ClientRepository;
use Concrete\Core\Permission\Checker;
use Concrete\Package\ConcreteApiProposal2022\Controller as ApiPackageController;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

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

    public function view($clientId)
    {
        /**
         * @var $clientRepository ClientRepository
         */
        $clientRepository = $this->app->make(ClientRepositoryInterface::class);
        $client = $clientRepository->findOneByIdentifier($clientId);
        if ($client) {
            $this->set('clientKey', $client->getClientKey());
            $this->set(
                'oauth2RedirectUrl',
                $this->app->make(RedirectUriFactory::class)->createDocumentationRedirectUri()
            );
        } else {
            throw new \Exception(t('Invalid API client.'));
        }
    }

}
