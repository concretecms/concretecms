<?php

namespace Concrete\Core\Express\Form\Validator\Routine;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Request;

class CSRFTokenRoutine implements RoutineInterface
{

    protected $request;
    protected $csrfValidator;

    public function __construct(Token $validator, Request $request)
    {
        $this->request = $request;
        $this->csrfValidator = $validator;
    }

    public function validate(ErrorList $error, Form $form, $requestType)
    {
        if (!$this->csrfValidator->validate('express_form', $this->request->request->get('ccm_token'))) {
            $error->add($this->csrfValidator->getErrorMessage());
            return false;
        }
        return true;
    }


}