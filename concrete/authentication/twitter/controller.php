<?php
namespace Concrete\Authentication\Twitter;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\Type\OAuth\OAuth1a\GenericOauth1aTypeController;
use Concrete\Core\Authentication\Type\Twitter\Factory\TwitterServiceFactory;
use Concrete\Core\Validation\CSRF\Token;
use OAuth\Common\Exception\Exception;
use OAuth\OAuth1\Service\Twitter;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

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
        return \Config::get('auth.twitter.registration.group');
    }

    /**
     * Twitter doesn't give us the users email.
     *
     * @return bool
     */
    public function supportsRegistration()
    {
        return \Config::get('auth.twitter.registration.enabled', false);
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
        \Config::save('auth.twitter.appid', $args['apikey']);
        \Config::save('auth.twitter.secret', $args['apisecret']);
        \Config::save('auth.twitter.registration.enabled', (bool) $args['registration_enabled']);
        \Config::save('auth.twitter.registration.group', intval($args['registration_group'], 10));
    }

    public function edit()
    {
        $this->set('form', \Loader::helper('form'));
        $this->set('apikey', \Config::get('auth.twitter.appid', ''));
        $this->set('apisecret', \Config::get('auth.twitter.secret', ''));

        $list = new \GroupList();
        $list->includeAllGroups();
        $this->set('groups', $list->getResults());
    }

    /**
     * We override this method because twitter doesn't give us the email, we have to have the user input it before we can create a user.
     *
     * @return null|\User
     *
     * @throws Exception
     */
    protected function attemptAuthentication()
    {
        $extractor = $this->getExtractor();
        $user_id = $this->getBoundUserID($extractor->getUniqueId());

        if ($user_id && $user_id > 0) {
            $user = \User::loginByUserID($user_id);
            if ($user && !$user->isError()) {
                return $user;
            }
        }

        if ($extractor->supportsEmail() && $user = \UserInfo::getByEmail($extractor->getEmail())) {
            if ($user && !$user->isError()) {
                throw new Exception('A user account already exists for this email, please log in and attach from your account page.');
            }
        }

        if ($this->supportsRegistration()) {
            /** @var FlashBagInterface $flashbag */
            $flashbag = \Session::getFlashBag();
            $flashbag->set('firstname', parent::getFirstName());
            $flashbag->set('lastname', parent::getLastName());
            $flashbag->set('username', parent::getUsername());
            $flashbag->set('token', $this->getToken());

            $response = \Redirect::to('/login/callback/twitter/handle_register/', id(new Token())->generate('twitter_register'));
            $response->send();
            exit;
        }

        return null;
    }

    public function handle_register($token = null)
    {

        /** @var FlashBagInterface $flashbag */
        $flashbag = \Session::getFlashBag();
        $this->firstName = array_shift($flashbag->peek('firstname'));
        $this->lastName = array_shift($flashbag->peek('lastName'));
        $this->username = array_shift($flashbag->peek('username'));
        $this->token = array_shift($flashbag->peek('token'));

        $token_helper = new Token();

        if (!$token_helper->validate('twitter_register', $token) && !$token_helper->validate('twitter_register') ||
            !$this->token) {
            $this->redirect('/login/');
            exit;
        }
        if (\Request::request('uEmail', false)) {
            $this->email = \Request::request('uEmail');

            $user = $this->createUser();
            if ($user && !$user->isError()) {
                return $this->completeAuthentication($user);
            }
        }

        $this->set('username', $this->username);
        $this->set('show_email', true);
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
