<?php
namespace Concrete\Authentication\Twitter;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\Type\OAuth\OAuth1a\GenericOauth1aTypeController;
use Concrete\Core\Authentication\Type\Twitter\Factory\TwitterServiceFactory;
use OAuth\OAuth1\Service\Twitter;
use Concrete\Core\User\User;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Routing\RedirectResponse;

class Controller extends GenericOauth1aTypeController
{
    public $apiMethods = array('handle_error', 'handle_success', 'handle_register');

    /**
     * @var string
     */
    protected $email;

    protected $username;

    protected $firstName;

    protected $lastName;

    public function registrationGroupID()
    {
        return $this->app->make('config')->get('auth.twitter.registration.group');
    }

    /**
     * Twitter doesn't give us the users email.
     *
     * @return bool
     */
    public function supportsRegistration()
    {
        return $this->app->make('config')->get('auth.twitter.registration.enabled', false);
    }

    /**
     * Twitter doesn't give us the users email.
     *
     * @return bool
     */
    public function supportsEmailResolution()
    {
        return false;
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fa fa-twitter"></i>';
    }

    public function getHandle()
    {
        return 'twitter';
    }

    /**
     * @return Twitter
     */
    public function getService()
    {
        if (!$this->service) {
            /** @var TwitterServiceFactory $factory */
            $factory = $this->app->make(TwitterServiceFactory::class);
            $this->service = $factory->createService();
        }

        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        $config = $this->app->make('config');
        $config->save('auth.twitter.appid', $args['apikey']);
        $config->save('auth.twitter.secret', $args['apisecret']);
        $config->save('auth.twitter.registration.enabled', (bool) $args['registration_enabled']);
        $config->save('auth.twitter.registration.group', intval($args['registration_group'], 10));
    }

    public function edit()
    {
        $config = $this->app->make('config');
        $this->set('form', $this->app->make('helper/form'));
        $this->set('apikey', $config->get('auth.twitter.appid', ''));
        $this->set('apisecret', $config->get('auth.twitter.secret', ''));

        $list = new \GroupList();
        $list->includeAllGroups();
        $this->set('groups', $list->getResults());
    }

    public function handle_detach_attempt()
    {

        if (!User::isLoggedIn()) {
            $response = new RedirectResponse(\URL::to('/login'), 302);
            $response->send();
            exit;
        }
        $user = new User();
        $uID = $user->getUserID();
        $namespace = $this->getHandle();


        $binding = $this->getBindingForUser($user);

        // Twitter Sign in is Oauth 1 so we can't revoke access only delete from the database
        try {
            /* @var \Concrete\Core\Database\Connection\Connection $database */
            $database = $this->app->make(Connection::class);
            $database->delete('OauthUserMap', ['user_id' => $uID, 'namespace' => $namespace, 'binding' => $binding]);
            $this->showSuccess(t('Successfully detached.'));
            exit;
        } catch (\Exception $e) {
            \Log::error(t('Deattach Error %s', $e->getMessage()));
            $this->showError(t('Unable to detach account.'));
            exit;
        }
    }

    public function supportsEmail()
    {
        return true;
    }

    public function getEmail()
    {
        if (!$this->email) {
            $this->email = parent::getEmail();
        }

        return $this->email;
    }

    public function getFirstName()
    {
        if (!$this->firstName) {
            $this->firstName = parent::getFirstname();
        }

        return $this->firstName;
    }

    public function getLastName()
    {
        if (!$this->lastName) {
            $this->lastName = parent::getLastName();
        }

        return $this->lastName;
    }

    public function getUsername()
    {
        if (!$this->username) {
            $this->username = parent::getUsername();
        }

        return $this->username;
    }
}
