<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Utility\Service\Validation\Strings;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class Oauth2 extends DashboardPageController
{
    public function view($userID = null)
    {
        $user = $this->app->make(UserInfoRepository::class)->getByID($userID);
        if ($user) {
            $up = new Checker($user);
            if ($up->canViewUser() && $up->canEditUser()) {
                $this->set('pageTitle', t('View/Edit %s', $user->getUserDisplayName()));
                $this->set('user', $user);
            } else {
                unset($user);
            }
        }

        if (!$user) {
            return $this->redirect('/dashboard/users');
        }
    }

    public function add($userID = null)
    {
        $this->view($userID);
        $this->render('/dashboard/users/oauth2/add');
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
        $this->render('/dashboard/users/oauth2/edit');
    }

    public function view_client($clientId = null)
    {
        if ($clientId) {
            $r = $this->app->make(ClientRepositoryInterface::class);
            if ($client = $r->findOneByIdentifier($clientId)) {
                $user = $client->getUser();
                if ($user) {
                    $this->view($user->getUserID());
                    $this->set('client', $client);
                    $this->render('/dashboard/users/oauth2/view_client');
                }
            }
        }
        if (!$client) {
            return $this->redirect('/dashboard/users');
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
            $user = $client->getUser();
            return $this->redirect('/dashboard/users/search', $user->getUserID());
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
            $user = $client->getUser();
            $this->entityManager->remove($client);
            $this->entityManager->flush();
            $this->flash('success', t('Integration deleted successfully.'));
            return $this->redirect('/dashboard/users/search', $user->getUserID());
        }
    }



    public function create($userID = null)
    {
        $this->add($userID);
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
            $client->setUser($this->get('user')->getEntityObject());
            $client->setRedirectUri('');
            $client->setName($this->request->request->get('name'));
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->flash('success', t('Integration saved successfully.'));
            return $this->redirect('/dashboard/users/oauth2/view_client', $client->getIdentifier());
        }
    }
}
