<?php

namespace Concrete\Core\User\PersistentAuthentication;

class CookieValue
{
    /**
     * The user ID.
     *
     * @var int
     */
    private $userID;

    /**
     * The handle of the authentication type.
     *
     * @var string
     */
    private $authenticationTypeHandle;

    /**
     * The unique token identifier.
     *
     * @var string
     */
    private $token;

    /**
     * @param int $userID The user ID
     * @param string $authenticationTypeHandle the authentication type handle
     * @param string $token the unique token identifier
     */
    public function __construct($userID, $authenticationTypeHandle, $token)
    {
        $this->userID = $userID;
        $this->authenticationTypeHandle = $authenticationTypeHandle;
        $this->token = $token;
    }

    /**
     * Get the user ID.
     *
     * @return int
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Get the handle of the authentication type.
     *
     * @return string
     */
    public function getAuthenticationTypeHandle()
    {
        return $this->authenticationTypeHandle;
    }

    /**
     * Get the unique token identifier.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
