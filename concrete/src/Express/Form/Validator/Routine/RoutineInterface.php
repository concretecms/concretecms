<?php

namespace Concrete\Core\Express\Form\Validator\Routine;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;

/**
 * @since 8.2.0
 */
interface RoutineInterface
{

    /**
     * @return bool
     */
    function validate(ErrorList $error, Form $form, $requestType);


}