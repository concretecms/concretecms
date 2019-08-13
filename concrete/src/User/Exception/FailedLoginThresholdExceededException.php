<?php

namespace Concrete\Core\User\Exception;

use OutOfBoundsException;

/**
 * @since 8.5.0
 */
class FailedLoginThresholdExceededException extends OutOfBoundsException implements UserException
{

}
