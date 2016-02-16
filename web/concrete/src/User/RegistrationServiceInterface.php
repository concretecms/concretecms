<?php
namespace Concrete\Core\User;

interface RegistrationServiceInterface
{
    public function create($data);
    public function createSuperUser($encryptedPassword, $email);
    public function createFromPublicRegistration($data);
}
