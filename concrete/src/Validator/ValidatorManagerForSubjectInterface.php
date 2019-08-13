<?php

namespace Concrete\Core\Validator;

/**
 * A generic validator manager interface that enables validating against many validators at once.
 * Compared to ValidatorManagerInterface, this interface supports both ValidatorInterface and ValidatorForSubjectInterface interfaces.
 * @since 8.4.2
 */
interface ValidatorManagerForSubjectInterface extends ValidatorForSubjectInterface, ValidatorManagerInterface
{
}
