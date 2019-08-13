<?php
namespace Concrete\Core\User;

/**
 * @since 8.2.0
 */
interface StatusServiceInterface
{
    public function sendEmailValidation($data);
}
