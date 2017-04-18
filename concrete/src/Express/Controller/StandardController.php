<?php
namespace Concrete\Core\Express\Controller;

use Concrete\Core\Application\Application;
use Concrete\Core\Express\Form\Processor\StandardProcessor;

class StandardController implements ControllerInterface
{

    protected $app;
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getContextRegistry()
    {
        return null;
    }

    public function getFormProcessor()
    {
        return $this->app->make(StandardProcessor::class);
    }


}
