<?php

namespace Concrete\Core\Encryption;

use Concrete\Core\Config\Repository\Repository;
use Hautelook\Phpass\PasswordHash;

class PasswordHasher
{
    /**
     * @var \Hautelook\Phpass\PasswordHash
     */
    private $phpassPasswordHash;

    /**
     * @param \Concrete\Core\Config\Repository\Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->phpassPasswordHash = new PasswordHash(
            $config->get('concrete.user.password.hash_cost_log2'),
            $config->get('concrete.user.password.hash_portable')
        );
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
        return $this->phpassPasswordHash->HashPassword($password);
    }

    /**
     * Check if a password corresponds to a stored hash previosly created with the hashPassword() method.
     *
     * @param string $password
     * @param string $storedHash
     */
    public function checkPassword($password, $storedHash)
    {
        return $this->phpassPasswordHash->CheckPassword($password, $storedHash);
    }
}
