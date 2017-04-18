<?php

namespace Concrete\Core\Express\Form\Processor;

use Concrete\Core\Express\Form\Validator\StandardValidator;
use Concrete\Core\Application\Application;
use Symfony\Component\HttpFoundation\Request;

class StandardProcessor implements ProcessorInterface
{

    protected $app;
    public function __construct(Application $app)
    {
        $this->app = $app;
    }


    public function getValidator(Request $request)
    {
        $validator = new StandardValidator($this->app, $this->app->make('error'), $request);
        return $validator;
    }

}