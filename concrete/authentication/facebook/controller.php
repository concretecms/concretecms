<?php

namespace Concrete\Authentication\Facebook;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\Type\Facebook\Factory\FacebookServiceFactory;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use Concrete\Core\Form\Service\Widget\GroupSelector;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\User;
use OAuth\OAuth2\Service\Facebook;

class Controller extends GenericOauth2TypeController
{
    public $apiMethods = ['handle_error', 'handle_success', 'revoke', 'handle_register'];

    public function registrationGroupID()
    {
        return $this->app->make('config')->get('auth.facebook.registration.group');
    }

    public function supportsRegistration()
    {
        return $this->app->make('config')->get('auth.facebook.registration.enabled', false);
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fab fa-facebook-f"></i>';
    }

    public function getHandle()
    {
        return 'facebook';
    }

    /**
     * @return Facebook
     */
    public function getService()
    {
        if (!$this->service) {
            /** @var FacebookServiceFactory $factory */
            $factory = $this->app->make(FacebookServiceFactory::class);
            $this->service = $factory->createService();
        }

        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        $config = $this->app->make('config');
        $config->save('auth.facebook.appid', (string) ($args['apikey'] ?? ''));
        $config->save('auth.facebook.secret', (string) ($args['apisecret'] ?? ''));
        $config->save('auth.facebook.registration.enabled', !empty($args['registration_enabled']));
        $config->save('auth.facebook.registration.group', ((int) ($args['registration_group'] ?? 0)) ?: null);
    }

    public function edit()
    {
        $rm = $this->app->make(ResolverManagerInterface::class);
        $config = $this->app->make('config');
        $this->set('groupSelector', $this->app->make(GroupSelector::class));
        $this->set('form', $this->app->make('helper/form'));
        $this->set('oauthRedirectUri', $rm->resolve(['/ccm/system/authentication/oauth2/facebook/callback']));
        $this->set('oauthDeauthorizeUri', $rm->resolve(['/login/callback/facebook/revoke']));
        $this->set('apikey', $config->get('auth.facebook.appid', ''));
        $this->set('apisecret', $config->get('auth.facebook.secret', ''));
        $this->set('registrationEnabled', (bool) $config->get('auth.facebook.registration.enabled'));
        $registrationGroupID = (int) $config->get('auth.facebook.registration.group');
        $registrationGroup = $registrationGroupID === 0 ? null : $this->app->make(GroupRepository::class)->getGroupById($registrationGroupID);
        $this->set('registrationGroup', $registrationGroup === null ? null : (int) $registrationGroup->getGroupID());
    }

    public function revoke()
    {
        $data = $this->parseSignedRequest($this->get('signed_request'));
        if ($data !== null) {
            $userID = $data['user_id'];
            if ($userID !== null && $userID !== '') {
                try {
                    $this->getBindingService()->clearBinding(null, $userID, 'facebook');
                } catch (\Exception $e) {
                    \Log::Error(t('Error detaching account : %s', $e->getMessage()));
                    $this->showError(t('Error detaching account'));
                }
                $this->showSuccess(t('Successfully detached.'));
                exit();
            }
            $this->showError(t('No user id found'));
        }
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

        $this->getService()->request('/' . $binding . '/permissions', 'DELETE');
        try {
            $this->getBindingService()->clearBinding($uID, $binding, $namespace, true);

            $this->showSuccess(t('Successfully detached.'));
            exit;
        } catch (\Exception $e) {
            \Log::error(t('Detach Error %s', $e->getMessage()));
            $this->showError(t('Unable to detach account.'));
            exit;
        }
    }

    protected function parseSignedRequest($signedRequest)
    {
        list($encodedSignature, $payload) = explode('.', $signedRequest, 2);

        $secret = $this->app->make('config')->get('auth.facebook.secret', '');

        // decode the data
        $signature = $this->base64_url_decode($encodedSignature);
        $data = json_decode($this->base64_url_decode($payload), true);

        // confirm the signature
        $expectedSignature = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($signature !== $expectedSignature) {
            $this->showError(t('Bad Signed JSON signature!'));

            return null;
        }

        return $data;
    }

    protected function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
