<?php

namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\Error;
use Symfony\Component\HttpFoundation\Request;

class Validator
{

    protected $request;
    protected $error;

    public function __construct(Error $error, Request $request)
    {
        $this->request = $request;
        $this->error = $error;
    }

    public function validate(Form $form)
    {
        $token = \Core::make('token');
        if (!$token->validate('express_form', $this->request->request->get('ccm_token'))) {
            $this->error->add($token->getErrorMessage());
        }
    }


}