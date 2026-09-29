<?php
namespace Concrete\Core\User;

interface RegistrationServiceInterface
{
    public function create($data);
    public function createSuperUser($encryptedPassword, $email, $username);
    public function createFromPublicRegistration($data);
}
