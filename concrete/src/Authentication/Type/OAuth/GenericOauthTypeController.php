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

abstract class GenericOauthTypeController extends AuthenticationTypeController
{
    public $apiMethods = ['handle_error', 'handle_success'];

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

    public function __construct(AuthenticationType $type = null)
    {
        parent::__construct($type);
        $manager = \Database::connection()->getSchemaManager();

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
            $error = \Session::get('oauth_last_error');
            \Session::set('oauth_last_error', null);
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
        \Session::set('oauth_last_error', $error);
    }

    public function handle_success($message = false)
    {
        if (!$message) {
            $message = \Session::get('oauth_last_message');
            \Session::set('oauth_last_message', null);
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
        \Session::set('oauth_last_message', $message);
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
            $user = \User::loginByUserID($user_id);
            if ($user && !$user->isError()) {
                return $user;
            }
        }

        if ($extractor->supportsEmail() && $user = \UserInfo::getByEmail($extractor->getEmail())) {
            if ($user && !$user->isError()) {
                throw new Exception(
                    'A user account already exists for this email, please log in and attach from your account page.');
            }
        }

        if ($this->supportsRegistration()) {
            $user = $this->createUser();

            return $user;
        }

        return null;
    }

    /**
     * @return \OAuth\UserData\Extractor\ExtractorInterface
     *
     * @throws \OAuth\UserData\Exception\UndefinedExtractorException
     */
    public function getExtractor($new = false)
    {
        if ($new || !$this->extractor) {
            $this->extractor = \Core::make('oauth_extractor', [$this->getService()]);
        }

        return $this->extractor;
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
     * @return bool|string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getBoundUserID($binding)
    {
        $result = \Database::connection()->executeQuery(
            'SELECT user_id FROM OauthUserMap WHERE namespace=? AND binding=?',
            [
                $this->getHandle(),
                $binding,
            ]);

        return $result->fetchColumn();
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

        $username = null;
        if ($this->supportsUsername()) {
            $username = $this->getUsername();
        }

        if ($username === null) {
            if ($first_name || $last_name) {
                $username = preg_replace('/[^a-z0-9\_]/', '_', strtolower($first_name . ' ' . $last_name));
                $username = trim(preg_replace('/_{2,}/', '_', $username), '_');
            } else {
                $username = preg_replace('/[^a-zA-Z0-9\_]/i', '_', strtolower(substr($email, 0, strpos($email, '@'))));
                $username = trim(preg_replace('/_{2,}/', '_', $username), '_');
            }
        }

        $unique_username = $username;
        $append = 1;

        while (\UserInfo::getByUserName($unique_username)) {
            // This is a heavy handed way to do this, but it must be done.
            $unique_username = $username . '_' . $append++;
        }

        $username = $unique_username;

        $data = [];
        $data['uName'] = $username;
        $data['uPassword'] = \Illuminate\Support\Str::random(256);
        $data['uEmail'] = $email;
        $data['uIsValidated'] = 1;

        $user_info = \UserInfo::add($data);

        if (!$user_info) {
            throw new Exception('Unable to create new account.');
        }

        if ($group_id = intval($this->registrationGroupID(), 10)) {
            $group = \Group::getByID($group_id);
            if ($group && is_object($group) && !$group->isError()) {
                $user = \User::getByUserID($user_info->getUserID());
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

        \User::loginByUserID($user_info->getUserID());

        $this->bindUser($user = \User::getByUserID($user_info->getUserID()), $this->getUniqueId());

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
     * @param \User $user
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
        $qb = \Database::connection()->createQueryBuilder();

        $or = $qb->expr()->orX();
        $or->add($qb->expr()->eq('user_id', intval($user_id, 10)));
        $or->add($qb->expr()->eq('binding', ':binding'));

        $and = $qb->expr()->andX();
        $and->add($qb->expr()->comparison('namespace', '=', ':namespace'));
        $and->add($or);

        $qb->delete('OauthUserMap')->where($and)
           ->setParameter(':namespace', $this->getHandle())
           ->setParameter(':binding', $binding)
           ->execute();

        return \Database::connection()->insert(
            'OauthUserMap',
            [
                'user_id' => $user_id,
                'binding' => $binding,
                'namespace' => $this->getHandle(),
            ]);
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
        $result = null;
        if (is_object($user)) {
            $userID = $user->getUserID();
        } else {
            $userID = (int) $user;
        }
        if ($userID) {
            $db = $this->app->make(Connection::class);
            $qb = $db->createQueryBuilder();
            $qb->select('oum.binding')
                ->from('OauthUserMap', 'oum')
                ->where($qb->expr()->eq('oum.user_id', ':user_id'))->setParameter('user_id', $userID)
                ->andWhere($qb->expr()->eq('oum.namespace', ':namespace'))->setParameter('namespace', $this->getHandle())
                ->setMaxResults(1);
            $rs = $qb->execute();
            /* @var \Concrete\Core\Database\Driver\PDOStatement $rs */
            $row = $rs->fetch();
            $rs->closeCursor();
            if ($row !== false) {
                $result = array_pop($row);
            }
        }

        return $result;
    }

    public function getUniqueId()
    {
        return $this->getExtractor()->getUniqueId();
    }

    abstract public function handle_authentication_attempt();

    abstract public function handle_authentication_callback();

    abstract public function handle_attach_attempt();

    abstract public function handle_attach_callback();
}
