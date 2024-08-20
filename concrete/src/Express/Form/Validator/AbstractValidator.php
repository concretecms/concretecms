<?php

namespace Concrete\Core\Express\Form\Validator;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\Form\Validator\Routine\RoutineInterface;

abstract class AbstractValidator implements ValidatorInterface
{

    /**
     * @var RoutineInterface
     */
    protected $routines = [];
    protected $error;

    public function addRoutine(RoutineInterface $routine)
    {
        $this->routines[] = $routine;
    }

    public function validate(Form $form, ?Entry $entry = null)
    {
        $valid = true;
        foreach($this->routines as $routine) {
            if (!$routine->validate($this->error, $form, $entry)) {
                $valid = false;
            }
        }

    }

    public function getErrorList()
    {
        return $this->error;
    }

}