<?php

namespace Concrete\Core\Encryption;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Legacy\PasswordHash;

class PasswordHasher
{
    /**
     * @var \Concrete\Core\Legacy\PasswordHash
     */
    private $phpassPasswordHash;

    /**
     * The hash algorithm to use for passwords
     * @var string
     */
    private $algorithm;

    /**
     * Options to provide when hashing and checking passwords
     * @var array
     */
    private $hashOptions;

    /**
     * @param \Concrete\Core\Config\Repository\Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->algorithm = $config->get('concrete.user.password.hash_algorithm') ?? PASSWORD_DEFAULT;
        $this->hashOptions = (array) ($config->get('concrete.user.password.hash_options', []) ?? []);

        $this->phpassPasswordHash = new PasswordHash(34, true);

        // @TODO Remove `hash_cost_log2` backwards compatibility in version 8
        $hashCost = (int) $config->get('concrete.user.password.hash_cost_log2', PASSWORD_BCRYPT_DEFAULT_COST);
        if (($this->hashOptions['cost'] ?? null) === null && $this->algorithm === PASSWORD_BCRYPT) {
            $this->hashOptions['cost'] = $hashCost;
        }
    }

    /**
     * Create a hash for a plain password.
     *
     * @param string $password
     *
     * @return string
     */
    public function hashPassword($password)
    {
        return password_hash($password, $this->algorithm, $this->hashOptions);
    }

    /**
     * Check if a password corresponds to a stored hash previosly created with the hashPassword() method.
     *
     * @param string $password
     * @param string $storedHash
     */
    public function checkPassword($password, $storedHash)
    {
        if ($this->isPortable($storedHash)) {
            return $this->phpassPasswordHash->checkPassword($password, $storedHash);
        }

        return password_verify($password, $storedHash);
    }

    /**
     * Determine whether the given hash needs to be rehashed
     */
    public function needsRehash(string $hash): bool
    {
        return $this->isPortable($hash) || password_needs_rehash($hash, $this->algorithm, $this->hashOptions);
    }

    /**
     * Determine whether a given hash is a portable phpass hash
     */
    private function isPortable(string $storedHash): bool
    {
        return strlen($storedHash) === 34 && strpos($storedHash, '$P$') === 0;
    }
}
