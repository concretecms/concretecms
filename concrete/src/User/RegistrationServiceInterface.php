<?php
namespace Concrete\Core\User;

/**
 * @since 5.7.5.4
 */
interface RegistrationServiceInterface
{
    public function create($data);
    public function createSuperUser($encryptedPassword, $email);
    public function createFromPublicRegistration($data);
}
