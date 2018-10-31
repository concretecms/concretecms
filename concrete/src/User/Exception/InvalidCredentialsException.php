<?php

namespace Concrete\Core\User\Exception;

class InvalidCredentialsException extends \InvalidArgumentException implements UserException
{

    protected $message = USER_INVALID;

}
