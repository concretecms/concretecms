<?php

namespace Concrete\Core\Express\Form\Processor;

use Concrete\Core\Express\Form\Validator\StandardValidator;
use Concrete\Core\Application\Application;

class StandardProcessor implements ProcessorInterface
{

    protected $app;
    public function __construct(Application $app)
    {
        $this->app = $app;
    }


    public function getValidator()
    {
        return $this->app->make(StandardValidator::class);
    }

}