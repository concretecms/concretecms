<?php
namespace Concrete\Core\Authentication\Type\OAuth;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\User\User;
use OAuth\Common\Exception\Exception;
use OAuth\Common\Service\AbstractService;
use OAuth\Common\Token\TokenInterface;
use OAuth\UserData\Extractor\Extractor;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Concrete\Core\Validation\CSRF\Token;

abstract class GenericOauthTypeController extends AuthenticationTypeController
{
    public $apiMethods = ['handle_error', 'handle_success', 'handle_register'];

    /**
     * @var \OAuth\Common\Service\AbstractService
     */
    protected $service;

    /**
     * @var Extractor
     */
    protected $extractor;

    /**
     * @var \OAuth\Common\Token\TokenInterface
     */
    protected $token;

    /* @var string $username */
    protected $username;

    /* @var string $email */
    protected $email;

    /* @var string $firstName */
    protected $firstName;

    /* @var string $lastName */
    protected $lastName;

    /* @var string $fullName */
    protected $fullName;

    /** @var BindingService|null The service used to manage oauth user bindings */
    protected $bindingService;

    public function __construct(AuthenticationType $type = null)
    {
        parent::__construct($type);
        $manager = $this->app->make(Connection::class)->getSchemaManager();

        if (!$manager->tablesExist('OauthUserMap')) {
            $schema = new \Doctrine\DBAL\Schema\Schema();
            $table = $schema->createTable('OauthUserMap');
            $table->addColumn('user_id', 'integer', ['unsigned' => true]);
            $table->addColumn('binding', 'string', ['length' => 255]);
            $table->addColumn('namespace', 'string', ['length' => 255]);

            $table->setPrimaryKey(['user_id', 'namespace']);
            $table->addUniqueIndex(['binding', 'namespace'], 'oauth_binding');

            $manager->createTable($table);
        }
    }

    /**
     * @return array
     */
    public function getAdditionalRequestParameters()
    {
        return [];
    }

    public function handle_error($error = false)
    {
        if (!$error) {
            $error = $this->app->make('session')->get('oauth_last_error');
            $this->app->make('session')->set('oauth_last_error', null);
        }
        if (!$error) {
            $error = 'An unexpected error occurred.';
        }

        $this->set('error', $error);
    }

    public function showError($error = null)
    {
        if ($error) {
            $this->markError($error);
        }
        id(new \RedirectResponse(\URL::to('/login/callback/' . $this->getHandle() . '/handle_error')))->send();
    }

    public function markError($error)
    {
        $this->app->make('session')->set('oauth_last_error', $error);
    }

    public function handle_success($message = false)
    {
        if (!$message) {
            $message = $this->app->make('session')->get('oauth_last_message');
            $this->app->make('session')->set('oauth_last_message', null);
        }
        if ($message) {
            $this->set('message', $message);
        }
    }

    public function showSuccess($message = null)
    {
        if ($message) {
            $this->markSuccess($message);
        }
        id(new \RedirectResponse(\URL::to('/login/callback/' . $this->getHandle() . '/handle_success')))->send();
    }

    public function markSuccess($message)
    {
        $this->app->make('session')->set('oauth_last_message', $message);
    }

    /**
     * Empty because we don't use the authenticate entry point.
     */
    public function authenticate()
    {
    }

    /**
     * Create a cookie hash to identify the user indefinitely.
     *
     * @param \User $u
     *
     * @return string Unique hash to be used to verify the users identity
     */
    public function buildHash(User $u)
    {
        return '';
    }

    /**
     * Hash authentication disabled for oauth.
     *
     * @param User  $u    object requesting verification
     * @param string $hash
     *
     * @return bool returns true if the hash is valid, false if not
     */
    public function verifyHash(User $u, $hash)
    {
        return false;
    }

    /**
     * @return \OAuth\Common\Token\TokenInterface
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function setToken(TokenInterface $token)
    {
        $this->token = $token;
    }

    /**
     * We now check if the user allows access to email address as twitter does not provide this and users can deny access to  on facebook).
     *
     * @return null|\User
     *
     * @throws Exception
     */
    protected function attemptAuthentication()
    {
        $extractor = $this->getExtractor();

        if (!$this->isValid()) {
            throw new Exception(
                'Invalid account, user cannot be logged in.');
        }

        $user_id = $this->getBoundUserID($extractor->getUniqueId());

        if ($user_id && $user_id > 0) {
            $user = User::loginByUserID($user_id);
            if ($user && !$user->isError()) {
                $this->app->make('session')->migrate();
                return $user;
            }
        }

        if ($extractor->supportsEmail() && $user = \UserInfo::getByEmail($extractor->getEmail())) {
            if ($user && !$user->isError()) {
                throw new Exception('A user account already exists for this email, please log in and attach from your account page.');
            }
        }

        if ($this->supportsRegistration()) {
            if ($extractor->getEmail() === null || empty($extractor->getEmail())) {

                /** @var FlashBagInterface $flashbag */
                $flashbag = $this->app->make('session')->getFlashBag();
                if ($this->supportsFullName()) {
                    $flashbag->set('fullName', $this->getFullName());
                } else {
                    $flashbag->set('firstName', $this->getFirstName());
                    $flashbag->set('lastName', $this->getLastName());
                }
                $flashbag->set('username', $this->getUsername());
                $flashbag->set('token', $this->getToken());


                $response = \Redirect::to('/login/callback/' . $this->getHandle() . '/handle_register/', id(new Token())->generate($this->getHandle() . '_register'));
                $response->send();
                exit;

            } else {
                $user = $this->createUser();

                return $user;
            }
        }

        return null;
    }

    public function handle_register($token = null)
    {

        /** @var FlashBagInterface $flashbag */
        $flashbag = $this->app->make('session')->getFlashBag();
        if ($this->supportsFullName()) {
            $this->fullName = array_shift($flashbag->peek('fullName'));
        } else {
            $this->firstName = array_shift($flashbag->peek('firstName'));
            $this->lastName = array_shift($flashbag->peek('lastName'));
        }
        $this->username = array_shift($flashbag->peek('username'));
        $this->token = array_shift($flashbag->peek('token'));

        $token_helper = new Token();

        if (!$token_helper->validate($this->getHandle().'_register', $token) && !$token_helper->validate($this->getHandle().'_register') ||
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
        if ($this->supportsFullName()) {
            $this->set('fullName', $this->fullName);
        }
        $this->set('token', $token_helper);
        $this->set('username', $this->username);
        $this->set('show_email', true);
    }

    /**
     * @return \OAuth\UserData\Extractor\ExtractorInterface
     *
     * @throws \OAuth\UserData\Exception\UndefinedExtractorException
     */
    public function getExtractor($new = false)
    {
        if ($new || !$this->extractor) {
            $this->extractor = $this->app->make('oauth_extractor', [$this->getService()]);
        }

        return $this->extractor;
    }

    public function form()
    {
        $this->set('user', $this->app->make(User::class));
        $this->set('authenticationType', $this->getAuthenticationType());
    }

    /**
     * @return AbstractService
     */
    abstract public function getService();

    protected function isValid()
    {
        return $this->extractor->supportsUniqueId();
    }

    /**
     * @param $binding
     *
     * @return int|bool False if no user id is bound
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getBoundUserID($binding)
    {
        $id = $this->getBindingService()->getBoundUserId($binding, $this->getHandle());
        return $id === null ? false : $id;
    }

    /**
     * Whether or not we will attempt to register the user.
     *
     * @return bool
     */
    abstract public function supportsRegistration();

    /**
     * Whether or not we will attempt to register the user.
     *
     * @return bool
     */
    abstract public function registrationGroupID();

    protected function createUser()
    {
        // Make sure that this extractor supports everything we need.
        if (!$this->supportsEmail() && $this->supportsUniqueId()) {
            throw new Exception('Email and unique ID support are required for user creation.');
        }

        // Make sure that email is verified if the extractor supports it.
        if ($this->supportsVerifiedEmail() && !$this->isEmailVerified()) {
            throw new Exception('Please verify your email with this service before attempting to log in.');
        }

        $email = $this->getEmail();
        if (\UserInfo::getByEmail($email)) {
            throw new Exception('Email is already in use.');
        }

        $first_name = '';
        $last_name = '';

        $name_support = [
            'full' => $this->supportsFullName(),
            'first' => $this->supportsFirstName(),
            'last' => $this->supportsLastName(),
        ];

        if ($name_support['first'] && $name_support['last']) {
            $first_name = $this->getFirstName();
            $last_name = $this->getLastName();
        } elseif ($name_support['full']) {
            $reversed_full_name = strrev($this->getFullName());
            list($reversed_last_name, $reversed_first_name) = explode(' ', $reversed_full_name, 2);

            $first_name = strrev($reversed_first_name);
            $last_name = strrev($reversed_last_name);
        }

        $userRegistration = $this->app->make('user/registration');

        $username = null;
        if ($this->supportsUsername()) {
            $username = $this->getUsername();
        }
        $username = $userRegistration->getNewUsernameFromUserDetails($email, $username, $first_name, $last_name);

        $data = [];
        $data['uName'] = $username;
        $data['uPassword'] = \Illuminate\Support\Str::random(256);
        $data['uEmail'] = $email;
        $data['uIsValidated'] = 1;

        $user_info = $userRegistration->create($data);

        if (!$user_info) {
            throw new Exception('Unable to create new account.');
        }

        if ($group_id = intval($this->registrationGroupID(), 10)) {
            $group = \Group::getByID($group_id);
            if ($group && is_object($group) && !$group->isError()) {
                $user = User::getByUserID($user_info->getUserID());
                $user->enterGroup($group);
            }
        }

        $attribs = \UserAttributeKey::getRegistrationList();
        if (!empty($attribs)) {
            $user_info->saveUserAttributesDefault($attribs);
        }

        $key = \UserAttributeKey::getByHandle('first_name');
        if ($key) {
            $user_info->setAttribute($key, $first_name);
        }

        $key = \UserAttributeKey::getByHandle('last_name');
        if ($key) {
            $user_info->setAttribute($key, $last_name);
        }

        User::loginByUserID($user_info->getUserID());

        $this->bindUser($user = User::getByUserID($user_info->getUserID()), $this->getUniqueId());

        return $user;
    }

    public function supportsEmail()
    {
        return $this->getExtractor()->supportsEmail();
    }

    public function supportsUniqueId()
    {
        return $this->getExtractor()->supportsUniqueId();
    }

    public function supportsVerifiedEmail()
    {
        return $this->getExtractor()->supportsVerifiedEmail();
    }

    public function isEmailVerified()
    {
        return $this->getExtractor()->isEmailVerified();
    }

    public function getEmail()
    {
        if ($this->email) {
            return $this->email;
        }
        return $this->getExtractor()->getEmail();
    }

    public function supportsFullName()
    {
        return $this->getExtractor()->supportsFullName();
    }

    public function supportsFirstName()
    {
        return $this->getExtractor()->supportsFirstName();
    }

    public function supportsLastName()
    {
        return $this->getExtractor()->supportsLastName();
    }

    public function getFirstName()
    {
        return $this->getExtractor()->getFirstName();
    }

    public function getLastName()
    {
        return $this->getExtractor()->getLastName();
    }

    public function getFullName()
    {
        return $this->getExtractor()->getFullName();
    }

    public function supportsUsername()
    {
        return $this->getExtractor()->supportsUsername();
    }

    public function getUsername()
    {
        return $this->getExtractor()->getUsername();
    }

    /**
     * @param User $user
     * @param       $binding
     *
     * @return int|null
     */
    public function bindUser(User $user, $binding)
    {
        return $this->bindUserID(intval($user->getUserID(), 10), $binding);
    }

    /**
     * @param $user_id
     * @param $binding
     *
     * @return int|null
     */
    public function bindUserID($user_id, $binding)
    {
        if (!$binding || !$user_id) {
            return null;
        }

        if ($this->getBindingService()->bindUserId($user_id, $binding, $this->getHandle())) {
            return 1;
        }

        return null;
    }

    /**
     * Get the binding associated to a specific user.
     *
     * @param \Concrete\Core\User\User|\Concrete\Core\User\UserInfo|\Concrete\Core\Entity\User\User|int $user
     *
     * @return string|null
     */
    public function getBindingForUser($user)
    {
        if (is_object($user)) {
            $userID = $user->getUserID();
        } else {
            $userID = (int) $user;
        }

        return $this->getBindingService()->getUserBinding($userID, $this->getHandle());
    }

    public function getUniqueId()
    {
        return $this->getExtractor()->getUniqueId();
    }

    abstract public function handle_authentication_attempt();

    abstract public function handle_authentication_callback();

    abstract public function handle_attach_attempt();

    abstract public function handle_attach_callback();

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
            $this->getBindingService()->clearBinding($uID, $namespace, $binding);
            $this->showSuccess(t('Successfully detached.'));
            exit;
        } catch (\Exception $e) {
            $this->logger->notice(t('Detach Error %s', $e->getMessage()));
            $this->showError(t('Unable to detach account.'));
            exit;
        }
    }

    /**
     * @return BindingService
     */
    protected function getBindingService()
    {
        if (!$this->bindingService) {
            $this->bindingService = $this->app->make(BindingService::class);
        }

        return $this->bindingService;
    }
}
