<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\RendererInterface;

abstract class Context implements ContextInterface
{
    protected $application;
    protected $renderer;

    public function __construct(Application $application, RendererInterface $renderer)
    {
        $this->application = $application;
        $this->renderer = $renderer;
    }

    public function getFormRenderer()
    {
        return $this->renderer;
    }

    public function getApplication()
    {
        return $this->application;
    }
}
