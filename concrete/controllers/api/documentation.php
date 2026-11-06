<?php

declare(strict_types=1);

namespace Concrete\Controller\Api;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Api\Documentation\RedirectUriFactory;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Documentation extends BackendInterfaceController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/api/documentation';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        $checker = new Checker();

        return $checker->canAccessApi(); // `access_api` custom task permission
    }

    public function view($clientId)
    {
        /**
         * @var \Concrete\Core\Entity\OAuth\ClientRepository $clientRepository
         */
        $clientRepository = $this->app->make(ClientRepositoryInterface::class);
        $client = $clientRepository->findOneByIdentifier($clientId);
        if (!$client) {
            throw new UserMessageException(t('Invalid API client.'));
        }
        $this->set('clientKey', $client->getClientKey());
        $this->set(
            'oauth2RedirectUrl',
            $this->app->make(RedirectUriFactory::class)->createDocumentationRedirectUri($client)
        );
        $this->set('openapiJsonUrl', (string) $this->app->make(ResolverManagerInterface::class)->resolve(['/ccm/system/api/openapi.json']));
    }
}
