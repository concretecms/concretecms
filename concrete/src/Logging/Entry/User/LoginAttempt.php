<?php

namespace Concrete\Core\Logging\Entry\User;

use Concrete\Core\Logging\Entry\EntryInterface;

/**
 * Log entry for user login attempts
 */
class LoginAttempt implements EntryInterface
{

    /**
     * The attempted login username
     *
     * @var string
     */
    protected $username;

    /**
     * The path that the login was submitted to
     *
     * @var string
     */
    protected $requestPath;

    /**
     * The group names that this user would have access to
     *
     * @var string[]
     */
    protected $groups;

    /**
     * The errors encountered when attempting login
     *
     * @var string[]
     */
    protected $errors;

    /**
     * LoginAttempt constructor.
     *
     * @param string $username The username used when attempting to log in
     * @param string $requestPath The path that is being requested
     * @param string[] $groups The groups the user would have access to
     * @param string[] $errors
     */
    public function __construct($username, $requestPath, $groups = [], $errors = [])
    {
        $this->username = $username;
        $this->requestPath = $requestPath;
        $this->groups = $groups;
        $this->errors = $errors;
    }

    /**
     * Convert this entry into something that can be inserted into the log
     *
     * @return string
     */
    public function getMessage()
    {
        $successString = $this->errors ? 'Failed' : 'Successful';
        return "{$successString} login attempt for \"{$this->username}\"";
    }

    /**
     * Get the added context for the log entry
     *
     * Ex: ["username": "...", "email": "...", "id": "...", "created_by": "..."]
     *
     * @return array
     */
    public function getContext()
    {
        return [
            'username' => $this->username,
            'requestPath' => $this->requestPath,
            'errors' => $this->errors,
            'groups' => $this->groups,
            'successful' => !$this->errors
        ];
    }
}
