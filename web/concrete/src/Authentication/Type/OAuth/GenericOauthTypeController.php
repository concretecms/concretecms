<?php
namespace Concrete\Core\Authentication\Type\OAuth;

use Concrete\Core\Authentication\AuthenticationTypeController;
use OAuth\Common\Exception\Exception;
use OAuth\Common\Service\AbstractService;
use OAuth\UserData\Extractor\Extractor;

abstract class GenericOauthTypeController extends AuthenticationTypeController
{

    public $apiMethods = array('handle_error', 'handle_success');

    /**
     * @var \OAuth\Common\Service\AbstractService
     */
    protected $service;

    /**
     * @var Extractor
     */
    protected $extractor;

    public function __construct()
    {
        $manager = \Database::connection()->getSchemaManager();

        if (!$manager->tablesExist('OauthUserMap')) {
            $schema = new \Doctrine\DBAL\Schema\Schema();
            $table = $schema->createTable('OauthUserMap');
            $table->addColumn('user_id', 'integer', array('unsigned' => true));
            $table->addColumn('binding', 'string', array('length' => 255));
            $table->addColumn('namespace', 'string', array('length' => 255));

            $table->setPrimaryKey(array('user_id', 'namespace'));
            $table->addUniqueIndex(array('binding', 'namespace'), 'oauth_binding');


            $manager->createTable($table);
        }
    }

    /**
     * @return AbstractService
     */
    abstract public function getService();

    /**
     * Whether or not we will attempt to register the user.
     *
     * @return bool
     */
    abstract public function supportsRegistration();

    /**
     * Whether or not we will attempt to resolve the user from the email address.
     *
     * @return bool
     */
    abstract public function supportsEmailResolution();

    /**
     * @return Array
     */
    public function getAdditionalRequestParameters() {
        return array();
    }

    /**
     * @param \User $user
     * @param       $binding
     * @return int|null
     */
    public function bindUser(\User $user, $binding)
    {
        return $this->bindUserID(intval($user->getUserID(), 10), $binding);
    }

    /**
     * @param $user_id
     * @param $binding
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
            array(
                'user_id'   => $user_id,
                'binding'   => $binding,
                'namespace' => $this->getHandle()
            ));
    }

    /**
     * @return null|\User
     * @throws Exception
     */
    protected function attemptAuthentication()
    {
        $extractor = $this->getExtractor();
        $user_id = $this->getBoundUserID($extractor->getUniqueId());

        if ($user_id && $user_id > 0) {
            $user = \User::loginByUserID($user_id);
            return $user;
        }

        if ($extractor->supportsEmail() && $user = \UserInfo::getByEmail($extractor->getEmail())) {
            if ($user && !$user->isError()) {
                if ($this->supportsEmailResolution()) {
                    $user = \User::loginByUserID($user_id);
                    return $user;
                } else {
                    throw new Exception('Email is already in use.');
                }
            }
        }

        if ($this->supportsRegistration()) {
            $user = $this->createUser();
            return $user;
        }

        return null;
    }

    protected function createUser()
    {
        $extractor = $this->getExtractor();

        // Make sure that this extractor supports everything we need.
        if (!$extractor->supportsEmail() && $extractor->supportsUniqueId()) {
            throw new Exception('Email and unique ID support are required for user creation.');
        }

        // Make sure that email is verified if the extractor supports it.
        if ($extractor->supportsVerifiedEmail() && !$extractor->isEmailVerified()) {
            throw new Exception('Please verify your email with this service before attempting to log in.');
        }

        $email = $extractor->getEmail();
        if (\UserInfo::getByEmail($email)) {
            throw new Exception('Email is already in use.');
        }

        $first_name = "";
        $last_name = "";

        $name_support = array(
            'full'  => $extractor->supportsFullName(),
            'first' => $extractor->supportsFirstName(),
            'last'  => $extractor->supportsLastName()
        );

        if ($name_support['first'] && $name_support['last']) {
            $first_name = $extractor->getFirstName();
            $last_name = $extractor->getLastName();
        } elseif ($name_support['full']) {
            $reversed_full_name = strrev($extractor->getFullName());
            list($reversed_last_name, $reversed_first_name) = explode(' ', $reversed_full_name, 2);

            $first_name = strrev($reversed_first_name);
            $last_name = strrev($reversed_last_name);
        }

        if ($extractor->supportsUsername()) {
            $username = $extractor->getUsername();
        } elseif ($first_name || $last_name) {
            $username = preg_replace('/[^a-z0-9\_]/', '_', strtolower($first_name . ' ' . $last_name));
            $username = trim('_', preg_replace('/_{2,}/', '_', $username));
        } else {
            $username = preg_replace('/[^a-zA-Z0-9\_]/i', '_', strtolower(substr($email, 0, strpos($email, '@'))));
            $username = trim('_', preg_replace('/_{2,}/', '_', $username));
        }

        $unique_username = $username;
        $append = 1;

        while (\UserInfo::getByUserName($unique_username)) {
            // This is a heavy handed way to do this, but it must be done.
            $unique_username = $username . '_' . $append++;
        }

        $username = $unique_username;

        $data = array();
        $data['uName'] = $username;
        $data['uPassword'] = "";
        $data['uEmail'] = $email;
        $data['uIsValidated'] = 1;

        $user_info = \UserInfo::add($data);

        if (!$user_info) {
            throw new Exception('Unable to create new account.');
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

        $this->bindUser($user = \User::getByUserID($user_info->getUserID()), $extractor->getUniqueId());
        return $user;
    }

    /**
     * @param $binding
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getBoundUserID($binding)
    {
        $result = \Database::connection()->executeQuery(
            'SELECT user_id FROM OauthUserMap WHERE namespace=? AND binding=?',
            array(
                $this->getHandle(),
                $binding
            ));

        return $result->fetchColumn();
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
     * @return \OAuth\UserData\Extractor\ExtractorInterface
     * @throws \OAuth\UserData\Exception\UndefinedExtractorException
     */
    public function getExtractor($new = false)
    {
        if ($new || !$this->extractor) {
            $this->extractor = \Core::make('oauth_extractor', $this->getService());
        }
        return $this->extractor;
    }

    /**
     * Empty because we don't use the authenticate entry point.
     */
    public function authenticate()
    {
    }

    /**
     * Create a cookie hash to identify the user indefinitely
     *
     * @param \User $u
     * @return string Unique hash to be used to verify the users identity
     */
    public function buildHash(\User $u)
    {
        return '';
    }

    /**
     * Hash authentication disabled for oauth
     *
     * @param \User  $u object requesting verification.
     * @param string $hash
     * @return bool        returns true if the hash is valid, false if not
     */
    public function verifyHash(\User $u, $hash)
    {
        return false;
    }

}
