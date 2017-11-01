<?php
namespace Concrete\Authentication\Facebook;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\Type\Facebook\Factory\FacebookServiceFactory;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use Concrete\Core\Routing\RedirectResponse;
use OAuth\OAuth2\Service\Facebook;
use Concrete\Core\User\User;
use Concrete\Core\Database\Connection\Connection;

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
        return '<i class="fa fa-facebook"></i>';
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
        $config->save('auth.facebook.appid', $args['apikey']);
        $config->save('auth.facebook.secret', $args['apisecret']);
        $config->save('auth.facebook.registration.enabled', (bool)$args['registration_enabled']);
        $config->save('auth.facebook.registration.group', intval($args['registration_group'], 10));
    }

    public function edit()
    {
        $config = $this->app->make('config');
        $this->set('form', $this->app->make('helper/form'));
        $this->set('apikey', $config->get('auth.facebook.appid', ''));
        $this->set('apisecret', $config->get('auth.facebook.secret', ''));

        $list = new \GroupList();
        $list->includeAllGroups();
        $this->set('groups', $list->getResults());
    }

    public function revoke()
    {
        $data = $this->parseSignedRequest($this->get('signed_request'));
        if ($data !== null) {
                $userID = $data['user_id'];
                if ($userID !== null && $userID !== '') {
                    /* @var \Concrete\Core\Database\Connection\Connection $database */
                    $database = $this->app->make(Connection::class);
                    try {
                        $database->delete('OauthUserMap', ['namespace' => 'facebook', 'binding' => $userID]);
                    } catch (\Exception $e) {
                        \Log::Error(t('Error detaching account : %s', $e->getMessage()));
                            $this->showError(t('Error detaching account'));
                    }
                    $this->showSuccess(t('Successfully detached'));
                    exit();
                } else {
                    $this->showError(t('No user id found'));
                }
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

        $this->getService()->request('/' . $binding . '/permissions', 'DELETE');
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
}
