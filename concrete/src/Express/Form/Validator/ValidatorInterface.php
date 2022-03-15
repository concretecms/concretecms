<?php

namespace Concrete\Core\Express\Form\Validator;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\Form\Validator\Routine\RoutineInterface;

interface ValidatorInterface
{

    function addRoutine(RoutineInterface $routine);

    /**
     * @return bool
     */
    function validate(Form $form, Entry $entry = null);


    /**
     * @return ErrorList
     */
    function getErrorList();

}