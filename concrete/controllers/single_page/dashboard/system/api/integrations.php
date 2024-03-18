<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Api;

use Concrete\Core\Api\IntegrationList;
use Concrete\Core\Api\OAuth\Client\ClientFactory;
use Concrete\Core\Api\OAuth\Command\CreateOAuthClientCommand;
use Concrete\Core\Api\OAuth\Command\DeleteOAuthClientCommand;
use Concrete\Core\Api\OAuth\Command\UpdateOAuthClientCommand;
use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Utility\Service\Validation\Strings;
use InvalidArgumentException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\Url\Url;
use Symfony\Component\HttpFoundation\JsonResponse;

class Integrations extends DashboardPageController
{

    protected $redirects = [];

    public function view()
    {
        $config = $this->app->make("config");
        $enable_api = (bool)$config->get('concrete.api.enabled');
        if (!$enable_api) {
            return $this->buildRedirect(['/dashboard/system/api/settings']);
        }

        $list = new IntegrationList($this->entityManager);
        if ($this->request->query->has('keywords')) {
            $list->filterByKeywords(h($this->request->query->get('keywords')));
        }
        $list->setItemsPerPage(20);
        $pagination = $this->app->make(PaginationFactory::class)->createPaginationObject($list);
        $this->set('pagination', $pagination);
        if ($pagination->getTotalResults() > 0) {
            $this->setThemeViewTemplate('full.php');
        }
        $this->set(
            'headerSearch',
            $this->app->make(ElementManager::class)->get('dashboard/api/integrations/search')
        );
        $this->set(
            'headerMenu',
            $this->app->make(ElementManager::class)->get('dashboard/api/integrations/menu')
        );
    }

    private function setScopes()
    {
        $scopes = $this->entityManager->getRepository(Scope::class)
            ->findAll();
        $this->set('scopes', $scopes);
    }
    public function add()
    {
        $this->set('pageTitle', t('Add API Integration'));
        $this->set('submitToken', $this->token->generate('create'));
        $this->set('submitAction', $this->action('create'));
        $this->setScopes();
        $this->render('/dashboard/system/api/integrations/form');
    }

    protected function validateIntegrationRequest()
    {
        $validator = $this->app->make(Strings::class);
        if (!$validator->notempty($this->request->request->get('name'))) {
            $this->error->add(t('You must specify a name for this integration'));
        }

        $redirects = (array) explode('|', (string) $this->request->request->get('redirect', ''));
        $redirects = array_filter($redirects, static function (string $redirect) use ($validator) {
            if ($validator->notempty($redirect)) {
                try {
                    $uri = Url::createFromUrl($redirect);

                    // Do some simple validation
                    return $uri->getHost()->get() && $uri->getScheme()->get();
                } catch (\Exception $e) {
                    return false;
                }
            }

            return false;
        });

        if (!count($redirects)) {
            $this->error->add(t('That doesn\'t look like a valid URL.'));
        }

        $this->redirects = array_unique($redirects ?: []);
    }

    public function edit($clientID = null)
    {
        $this->set('pageTitle', t('Update API Integration'));
        $this->setScopes();
        $this->view_client($clientID);
        $this->set('submitToken', $this->token->generate('update'));
        $this->set('submitAction', $this->action('update', $clientID));
        $this->render('/dashboard/system/api/integrations/form');
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

            $command = new UpdateOAuthClientCommand($client->getIdentifier());
            $command->setName($this->request->request->get('name'));
            $command->setRedirect(implode('|', $this->redirects));
            $command->setEnableDocumentation($this->request->request->getBoolean('enableDocumentation', false));
            $command->setConsentType($this->request->request->get('consentType', null));
            $hasCustomScopes = $this->request->request->getBoolean('hasCustomScopes', false);
            if ($hasCustomScopes) {
                $command->setHasCustomScopes(true);
                $command->setCustomScopes((array) $this->request->request->get('customScopes'));
            }

            $client = $this->app->executeCommand($command);

            $this->flash('success', t('Integration updated successfully.'));

            return new JsonResponse($client);

        } else {
            return new JsonResponse($this->error);
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

            $command = new DeleteOAuthClientCommand($client->getIdentifier());
            $this->app->executeCommand($command);

            $this->flash('success', t('Integration deleted successfully.'));
            return $this->redirect('/dashboard/system/api/integrations');
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
            return new JsonResponse($this->error);
        }

        $command = new CreateOAuthClientCommand();
        $command->setName($this->request->request->get('name'));
        $command->setRedirect($this->request->request->get('redirect'));
        $command->setEnableDocumentation($this->request->request->getBoolean('enableDocumentation', false));
        $command->setConsentType($this->request->request->get('consentType', null));

        $hasCustomScopes = $this->request->request->getBoolean('hasCustomScopes', false);
        if ($hasCustomScopes) {
            $command->setHasCustomScopes(true);
            $command->setCustomScopes((array) $this->request->request->get('customScopes'));
        }

        [$client, $credentials] = $this->app->executeCommand($command);

        $this->flash('success', t('Integration saved successfully.'));
        $this->flash('clientSecret', $credentials->getSecret());

        return new JsonResponse($client);
    }

}
