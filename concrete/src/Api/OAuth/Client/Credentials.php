<?php

namespace Concrete\Core\Api\OAuth\Client;

/**
 * OAuth Client Credentials
 * A value object for communicating plain text keys and secrets
 */
class Credentials
{

    /** @var string */
    private $key;

    /** @var string */
    private $secret;

    /**
     * Credentials constructor.
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Get the associated key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get the associated secret
     * WARNING: This is a secure string and is meant to be kept secret. Be sure to hash this value before storing to a
     * database.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }
}
