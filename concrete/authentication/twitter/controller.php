<?php

namespace Concrete\Authentication\Twitter;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\Type\OAuth\OAuth1a\GenericOauth1aTypeController;
use Concrete\Core\Authentication\Type\Twitter\Factory\TwitterServiceFactory;
use Concrete\Core\Form\Service\Widget\GroupSelector;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\User;
use OAuth\OAuth1\Service\Twitter;

class Controller extends GenericOauth1aTypeController
{
    public $apiMethods = ['handle_error', 'handle_success', 'handle_register'];

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
        return '<i class="fab fa-twitter"></i>';
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
        $config->save('auth.twitter.appid', trim((string) ($args['apikey'] ?? '')));
        $config->save('auth.twitter.secret', trim((string) ($args['apisecret'] ?? '')));
        $config->save('auth.twitter.registration.enabled', !empty($args['registration_enabled']));
        $config->save('auth.twitter.registration.group', ((int) ($args['registration_group'] ?? 0)) ?: null);
    }

    public function edit()
    {
        $config = $this->app->make('config');
        $this->set('groupSelector', $this->app->make(GroupSelector::class));
        $this->set('form', $this->app->make('helper/form'));
        $this->set('callbackUrl', $this->app->make(ResolverManagerInterface::class)->resolve(['/ccm/system/authentication/oauth2/twitter/callback']));
        $this->set('apikey', (string) $config->get('auth.twitter.appid', ''));
        $this->set('apisecret', (string) $config->get('auth.twitter.secret', ''));
        $this->set('registrationEnabled', (bool) $config->get('auth.twitter.registration.enabled'));
        $registrationGroupID = (int) $config->get('auth.twitter.registration.group');
        $registrationGroup = $registrationGroupID === 0 ? null : $this->app->make(GroupRepository::class)->getGroupById($registrationGroupID);
        $this->set('registrationGroup', $registrationGroup === null ? null : (int) $registrationGroup->getGroupID());
    }

    public function handle_detach_attempt()
    {
        $user = $this->app->make(User::class);
        if (!$user->isRegistered()) {
            $response = new RedirectResponse(\URL::to('/login'), 302);
            $response->send();
            exit;
        }
        $uID = $user->getUserID();
        $namespace = $this->getHandle();

        $binding = $this->getBindingForUser($user);

        // Twitter Sign in is Oauth 1 so we can't revoke access only delete from the database
        try {
            $this->getBindingService()->clearBinding($uID, $binding, $namespace, true);
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
