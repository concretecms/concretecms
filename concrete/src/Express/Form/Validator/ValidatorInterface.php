<?php

namespace Concrete\Core\Express\Form\Validator;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\Form\Validator\Routine\RoutineInterface;

interface ValidatorInterface
{

    const REQUEST_TYPE_ADD = 1;
    const REQUEST_TYPE_UPDATE = 2;

    function addRoutine(RoutineInterface $routine);

    /**
     * @return bool
     */
    function validate(Form $form, $requestType);


    /**
     * @return ErrorList
     */
    function getErrorList();

}