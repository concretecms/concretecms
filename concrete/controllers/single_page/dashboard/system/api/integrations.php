<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Api;

use Concrete\Core\Api\OAuth\Client\ClientFactory;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Utility\Service\Validation\Strings;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\Url\Url;

class Integrations extends DashboardPageController
{
    public function view()
    {
        return $this->redirect('/dashboard/system/api');
    }

    public function add()
    {
        $this->render('/dashboard/system/api/integrations/add');
    }

    protected function validateIntegrationRequest()
    {
        $validator = $this->app->make(Strings::class);
        if (!$validator->notempty($this->request->request->get('name'))) {
            $this->error->add(t('You must specify a name for this integration'));
        }

        $redirect = $this->request->request->get('redirect');
        if (!$validator->notempty($redirect)) {
            $this->error->add(t('You must specify a redirect url for this integration'));
        }

        try {
            $uri = Url::createFromUrl($redirect);

            // Do some simple validation
            if (!$uri->getHost() || !$uri->getScheme()) {
                throw new \RuntimeException('Invalid URI');
            }
        } catch (\Exception $e) {
            $this->error->add(t('That doesn\'t look like a valid URL.'));
        }
    }

    public function edit($clientID = null)
    {
        $this->view_client($clientID);
        $this->render('/dashboard/system/api/integrations/edit');
    }

    public function view_client($clientId = null)
    {
        if ($clientId) {
            $r = $this->app->make(ClientRepositoryInterface::class);
            if ($client = $r->findOneByIdentifier($clientId)) {
                $this->set('client', $client);
                $this->render('/dashboard/system/api/integrations/view_client');
            }
        }
        if (!$client) {
            return $this->view();
        }
    }

    public function update($clientID = null)
    {
        $this->edit($clientID);
        $this->validateIntegrationRequest();
        if (!$this->token->validate('update')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            /** @var Client $client */
            $client = $this->get('client');
            $client->setName($this->request->request->get('name'));
            $client->setRedirectUri($this->request->request->get('redirect'));

            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->flash('success', t('Integration saved successfully.'));
            return $this->redirect('/dashboard/system/api/integrations/', 'view_client', $client->getIdentifier());
        }
    }

    public function delete()
    {
        $this->edit($this->request->request->get('clientID'));
        if (!$this->token->validate('delete')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            /** @var Client $client */
            $client = $this->get('client');
            $this->entityManager->remove($client);
            $this->entityManager->flush();
            $this->flash('success', t('Integration deleted successfully.'));
            return $this->redirect('/dashboard/system/api/settings');
        }
    }

    /**
     * Request handler to create new client objects
     *
     * This method:
     * 1. Makes sure a name exists
     * 2. Validates the request token `create`
     * 3. Returns if any errors exist
     * 4. Uses a ClientFactory to create a new client object
     * 5. Uses password_hash on the client secret before storing to the database
     * 6. Stores the secret to a session flash bag
     * 7. Redirects to the view_client action
     */
    public function create()
    {
        $this->add();
        $this->validateIntegrationRequest();
        if (!$this->token->validate('create')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if ($this->error->has()) {
            return;
        }

        $factory = $this->app->make(ClientFactory::class);
        $credentials = $factory->generateCredentials();

        // Create a new client while hashing the new secret
        $client = $factory->createClient(
            $this->request->request->get('name'),
            $this->request->request->get('redirect'),
            [],
            $credentials->getKey(),
            password_hash($credentials->getSecret(), PASSWORD_DEFAULT)
        );

        // Persist the new client to the database
        $this->entityManager->persist($client);
        $this->entityManager->flush();
        $this->flash('success', t('Integration saved successfully.'));
        $this->flash('clientSecret', $credentials->getSecret());

        return $this->redirect('/dashboard/system/api/integrations/', 'view_client', $client->getIdentifier());
    }

}
