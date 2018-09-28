<?php
namespace Concrete\Core\User;

interface StatusServiceInterface
{
    public function sendEmailValidation($data);
}
