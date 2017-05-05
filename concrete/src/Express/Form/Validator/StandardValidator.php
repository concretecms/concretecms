<?php

namespace Concrete\Core\Express\Form\Validator;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\Form\Validator\Routine\CheckPermissionsRoutine;
use Concrete\Core\Express\Form\Validator\Routine\CSRFTokenRoutine;
use Concrete\Core\Express\Form\Validator\Routine\ValidateAttributesRoutine;
use Symfony\Component\HttpFoundation\Request;

class StandardValidator extends AbstractValidator
{

    protected $request;
    protected $error;

    public function __construct(Application $app, ErrorList $error, Request $request)
    {
        $this->request = $request;
        $this->error = $error;
        $this->addRoutine(new CheckPermissionsRoutine());
        $this->addRoutine(new CSRFTokenRoutine($app->make('token'), $request));
        $this->addRoutine(new ValidateAttributesRoutine($request));
    }

}