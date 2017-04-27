<?php

namespace Concrete\Core\Express\Form\Validator\Routine;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Symfony\Component\HttpFoundation\Request;

class ValidateAttributesRoutine implements RoutineInterface
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function validate(ErrorList $error, Form $form, $requestType)
    {
        $valid = true;
        foreach ($form->getControls() as $control) {
            $type = $control->getControlType();
            $validator = $type->getValidator($control);
            if (is_object($validator)) {
                $e = $validator->validateRequest($control, $this->request);
                if (is_object($e) && $e->has()) {
                    $valid = false;
                    $error->add($e);
                }
            }
        }
        return $valid;
    }


}