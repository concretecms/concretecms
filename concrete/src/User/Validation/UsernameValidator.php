<?php

namespace Concrete\Core\User\Validation;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Exception;

class UsernameValidator
{
    /**
     * Validation flag: everything is ok.
     *
     * @var int
     */
    const E_OK = 0b00000000; // 0

    /**
     * Validation flag: not a string (or string is empty).
     *
     * @var int
     */
    const E_INVALID_STRING = 0b00000001; // 1

    /**
     * Validation flag: username is too short.
     *
     * @var int
     */
    const E_TOO_SHORT = 0b10; // 2

    /**
     * Validation flag: username is too long.
     *
     * @var int
     */
    const E_TOO_LONG = 0b100; // 4

    /**
     * Validation flag: username contains invalid characters.
     *
     * @var int
     */
    const E_INVALID_CHARACTERS = 0b1000; // 8

    /**
     * Validation flag: username already in use.
     *
     * @var int
     */
    const E_IN_USE = 0b10000; // 16

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * Minimum username length (null: none, false: not yet initialized).
     *
     * @var int|null|false
     */
    private $minimumLength = false;

    /**
     * Maximum username length (null: none, false: not yet initialized).
     *
     * @var int|null|false
     */
    private $maximumLength = false;

    /**
     * Characters allowed at the beginning/end of usernames, with regular expressions syntax (null: not yet initialized).
     *
     * @var string|null
     */
    private $allowedCharactersBoundary = null;

    /**
     * Characters allowed in the middle of usernames, with regular expressions syntax (null: not yet initialized).
     *
     * @var string|null
     */
    private $allowedCharactersMiddle = null;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Config\Repository\Repository $config
     * @param \Concrete\Core\Database\Connection\Connection $connection
     */
    public function __construct(Application $app, Repository $config, Connection $connection)
    {
        $this->app = $app;
        $this->config = $config;
        $this->connection = $connection;
    }

    /**
     * Get the minimum username length.
     *
     * @return int|null
     */
    public function getMinimumLength()
    {
        if ($this->minimumLength === false) {
            $this->setMinimumLength($this->config->get('concrete.user.username.minimum'));
        }

        return $this->minimumLength;
    }

    /**
     * Set the minimum username length.
     *
     * @param int|null $minimumLength
     *
     * @return $this
     */
    public function setMinimumLength($minimumLength)
    {
        $this->minimumLength = empty($minimumLength) ? null : (int) $minimumLength;

        return $this;
    }

    /**
     * Get the maximum username length.
     *
     * @return int|null
     */
    public function getMaximumLength()
    {
        if ($this->maximumLength === false) {
            $this->setMaximumLength($this->config->get('concrete.user.username.maximum'));
        }

        return $this->maximumLength;
    }

    /**
     * Set the maximum username length.
     *
     * @param int|null $maximumLength
     *
     * @return $this
     */
    public function setMaximumLength($maximumLength)
    {
        $this->maximumLength = empty($maximumLength) ? null : (int) $maximumLength;

        return $this;
    }

    /**
     * Get the characters allowed at the beginning/end of usernames, with regular expressions syntax.
     *
     * @return string
     *
     * @example 'A-Za-z0-9'
     */
    public function getAllowedCharactersBoundary()
    {
        if ($this->allowedCharactersBoundary === null) {
            $this->setAllowedCharactersBoundary($this->config->get('concrete.user.username.allowed_characters.boundary'));
        }

        return $this->allowedCharactersBoundary;
    }

    /**
     * Set the characters allowed at the beginning/end of usernames, with regular expressions syntax.
     *
     * @param string $allowedCharacters
     *
     * @return $this
     *
     * @example 'A-Za-z0-9'
     */
    public function setAllowedCharactersBoundary($allowedCharacters)
    {
        $this->allowedCharactersBoundary = (string) $allowedCharacters;

        return $this;
    }

    /**
     * Get the characters allowed in the middle of usernames, with regular expressions syntax.
     *
     * @return string
     *
     * @example 'A-Za-z0-9_\.'
     */
    public function getAllowedCharactersMiddle()
    {
        if ($this->allowedCharactersMiddle === null) {
            $this->setAllowedCharactersMiddle($this->config->get('concrete.user.username.allowed_characters.middle'));
        }

        return $this->allowedCharactersMiddle;
    }

    /**
     * Set the characters allowed in the middle of usernames, with regular expressions syntax.
     *
     * @param string $allowedCharacters
     *
     * @return $this
     *
     * @example 'A-Za-z0-9_\.'
     */
    public function setAllowedCharactersMiddle($allowedCharacters)
    {
        $this->allowedCharactersMiddle = (string) $allowedCharacters;

        return $this;
    }

    /**
     * Check if a username is valid.
     *
     * @param string|mixed $username the username to be ckecked
     * @param int|null $userID the ID of the user whose the username is/will be associated to
     *
     * @return int One or more of the UsernameValidator::E_... flags
     */
    public function check($username, $userID = null)
    {
        $result = static::E_OK;

        $username = $this->normalizeString($username);
        if ($username === '') {
            $result |= static::E_INVALID_STRING;
        } else {
            $result |= $this->checkLength($username);
            $result |= $this->checkCharacters($username);
            if ($result === static::E_OK) {
                $result |= $this->checkUnique($username, $userID);
            }
        }

        return $result;
    }

    /**
     * Check if a username has the allowed length.
     *
     * @param string $username
     *
     * @return int One or more of the UsernameValidator::E_... flags
     */
    public function checkLength($username)
    {
        $username = $this->normalizeString($username);
        if ($username === '') {
            $result |= static::E_INVALID_STRING;
        } else {
            $result = static::E_OK;
            $usernameLength = mb_strlen($username);
            $minLength = $this->getMinimumLength();
            if ($minLength !== null && $usernameLength < $minLength) {
                $result |= static::E_TOO_SHORT;
            }
            $maxLength = $this->getMaximumLength();
            if ($maxLength !== null && $usernameLength > $maxLength) {
                $result |= static::E_TOO_LONG;
            }
        }

        return $result;
    }

    /**
     * Check if a username contains only the allowed characters.
     *
     * @param string $username
     *
     * @return int One or more of the UsernameValidator::E_... flags
     */
    public function checkCharacters($username)
    {
        $username = $this->normalizeString($username);
        if ($username === '') {
            $result |= static::E_INVALID_STRING;
        } else {
            $result = static::E_OK;
            $rx = $this->getValidCharactersRegularExpression();
            $match = preg_match($rx, $username);
            if ($match === false) {
                throw new Exception(t('The list of valid username characters is not valid.'));
            }
            $result = $match ? static::E_OK : static::E_INVALID_CHARACTERS;
        }

        return $result;
    }

    /**
     * Check if a username is already in use.
     *
     * @param string $username
     * @param int|null $userID the ID of the user whose the username is/will be associated to
     *
     * @return int One or more of the UsernameValidator::E_... flags
     */
    public function checkUnique($username, $userID = null)
    {
        $username = $this->normalizeString($username);
        if ($username === '') {
            $result = static::E_INVALID_STRING;
        } else {
            $qb = $this->connection->createQueryBuilder();
            $qb
            ->select('u.uID')
            ->from('Users', 'u')
            ->where($qb->expr()->eq('u.uName', $qb->createNamedParameter($username)))
            ;
            if (!empty($userID)) {
                $qb->andWhere($qb->expr()->neq('u.uID', $qb->createNamedParameter($userID)));
            }
            $result = $qb->execute()->fetchColumn() === false ? static::E_OK : static::E_IN_USE;
        }

        return $result;
    }

    /**
     * Describe the errors returned by the check functions.
     *
     * @param int $flags One or more of the UsernameValidator::E_... flags
     *
     * @return \Concrete\Core\Error\ErrorList\ErrorList
     */
    public function describeError($flags)
    {
        $result = $this->app->make('error');
        $flags = (int) $flags;
        if ($flags & static::E_INVALID_STRING) {
            $result->add(t('The username is empty.'));
        }
        if ($flags & static::E_TOO_SHORT) {
            $result->add(t('A username must be at least %s characters long.', $this->getMinimumLength()));
        }
        if ($flags & static::E_TOO_LONG) {
            $result->add(t('A username cannot be more than %s characters long.', $this->getMaximumLength()));
        }
        if ($flags & static::E_INVALID_CHARACTERS) {
            $boundary = $this->getAllowedCharactersBoundary();
            $middle = $this->getAllowedCharactersMiddle();
            if ($boundary === $middle) {
                $result->add(t('A username can only contains these characters: %s', $middle));
            } else {
                $result->add(t('A username can only contains the characters "%1$s" and can only start/end with "%2$s".', $middle, $boundary));
            }
        }
        if ($flags & static::E_IN_USE) {
            $result->add(t('The username is already used by another account.'));
        }
        if ($flags !== 0 && !$result->has()) {
            $result->add(t('The username is not valid.'));
        }

        return $result;
    }

    /**
     * Check if a username is a non empty string.
     *
     * @param string|mixed $username
     *
     * @return string returns an empty string of $username is not a string, $username otherwise
     */
    protected function normalizeString($username)
    {
        return is_string($username) ? $username : '';
    }

    /**
     * Get the regular expression to be used to check the username.
     *
     * @return string
     *
     * @example '/^[A-Za-z0-9]([A-Za-z0-9 ]*[A-Za-z0-9])?/
     */
    protected function getValidCharactersRegularExpression()
    {
        $boundary = '[' . $this->getAllowedCharactersBoundary() . ']';
        $middle = '[' . $this->getAllowedCharactersMiddle() . ']';

        return "/^{$boundary}({$middle}*{$boundary})?$/";
    }
}
