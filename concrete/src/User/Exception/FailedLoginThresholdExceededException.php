<?php

namespace Concrete\Core\User\Exception;

use OutOfBoundsException;

class FailedLoginThresholdExceededException extends OutOfBoundsException implements UserException
{

}
