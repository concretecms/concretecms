<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Api;

use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Utility\Service\Validation\Strings;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

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
            $client = $this->get('client');
            $client->setName($this->request->request->get('name'));
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->flash('success', t('Integration saved successfully.'));
            return $this->redirect('/dashboard/system/api/settings');
        }
    }

    public function delete()
    {
        $this->edit($this->request->request->get('clientID'));
        if (!$this->token->validate('delete')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $client = $this->get('client');
            $this->entityManager->remove($client);
            $this->entityManager->flush();
            $this->flash('success', t('Integration deleted successfully.'));
            return $this->redirect('/dashboard/system/api/settings');
        }
    }



    public function create()
    {
        $this->add();
        $this->validateIntegrationRequest();
        if (!$this->token->validate('create')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $identifier = new Identifier();
            $key = $identifier->getString(32);
            $secret = bin2hex(random_bytes(32));
            $client = new Client();
            $client->setClientKey($key);
            $client->setClientSecret($secret);
            $client->setRedirectUri('');
            $client->setName($this->request->request->get('name'));
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->flash('success', t('Integration saved successfully.'));
            return $this->redirect('/dashboard/system/api/integrations/', 'view_client', $client->getIdentifier());
        }
    }
}
