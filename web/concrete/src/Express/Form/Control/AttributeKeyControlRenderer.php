<?php

namespace Concrete\Core\Express\Form\Control;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;
use Concrete\Core\Foundation\Environment;
use Doctrine\ORM\EntityManagerInterface;

class AttributeKeyControlRenderer implements RendererInterface
{

    protected $factory;

    public function build(RendererFactory $factory)
    {
        $this->factory = $factory;
    }

    protected function getAttributeKeyObject()
    {
        return $this->factory->getControl()->getAttributeKey();
    }

    public function render()
    {
        $ak = $this->getAttributeKeyObject();
        if (is_object($ak)) {

            $template = $this->factory->getApplication()->make('environment')->getPath(
                DIRNAME_ELEMENTS .
                '/' . DIRNAME_EXPRESS .
                '/' . DIRNAME_EXPRESS_FORM_CONTROLS .
                '/attribute_key.php'
            );


            $view = new EntityPropertyControlView($this->factory);
            $view->addScopeItem('key', $ak);
            return $view->render($template);
        }
    }


}