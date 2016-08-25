<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\RendererInterface;
use Concrete\Core\Filesystem\TemplateLocator;

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

    public function getContextHandle()
    {
        $class = get_class($this);
        $class = substr($class, strrpos($class, '\\') + 1);
        $class = substr($class, 0, strpos($class, 'Context'));
        $class = $this->application->make('helper/text')->handle($class);
        return $class;
    }

    protected function getTemplatePath(Template $template)
    {
        $path = DIRNAME_ELEMENTS
            . DIRECTORY_SEPARATOR
            . DIRNAME_EXPRESS
            . DIRECTORY_SEPARATOR
            . DIRNAME_EXPRESS_FORM_CONTROLS
            . DIRECTORY_SEPARATOR
            . $this->getContextHandle()
            . DIRECTORY_SEPARATOR
            . $template->getTemplateHandle() . '.php';
        return $path;
    }

    public function getTemplateLocator(Template $template)
    {
        $locator = new TemplateLocator();
        $locator->addLocation($this->getTemplatePath($template));
        return $locator;
    }

}
