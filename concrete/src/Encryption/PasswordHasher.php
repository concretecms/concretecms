<?php

namespace Concrete\Core\Encryption;

use Concrete\Core\Config\Repository\Repository;

class PasswordHasher
{

    /** @var int|null */
    private $cost = null;

    /**
     * @param \Concrete\Core\Config\Repository\Repository $config
     */
    public function __construct(Repository $config)
    {
        $cost = $config->get('concrete.user.password.hash_cost_log2', null);
        if ($cost !== null) {
            $this->cost = (int) $cost;
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
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => $this->cost ?: PASSWORD_BCRYPT_DEFAULT_COST
        ]);
    }

    /**
     * Check if a password corresponds to a stored hash previosly created with the hashPassword() method.
     *
     * @param string $password
     * @param string $storedHash
     */
    public function checkPassword($password, $storedHash)
    {
        return password_verify($password, $storedHash);
    }
}
