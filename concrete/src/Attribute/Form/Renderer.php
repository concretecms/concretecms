<?php
namespace Concrete\Core\Attribute\Form;

use Concrete\Core\Attribute\Form\Control\View\View;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Form\Control\ViewInterface;

class Renderer
{

    protected $context;
    protected $object;

    public function __construct(ContextInterface $context, ObjectInterface $object = null)
    {
        $this->context = $context;
        $this->object = $object;
    }

    protected function getKey($ak)
    {
        $key = is_object($ak) ? $ak : $this->object->getObjectAttributeCategory()
            ->getAttributeKeyByHandle($ak);
        return $key;
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param ContextInterface $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    protected function getView($ak)
    {
        $key = $this->getKey($ak);
        $view = $key->getControlView($this->context);
        return $view;
    }

    public function buildView($ak)
    {
        $ak = $this->getKey($ak);
        $view = $this->getView($ak);
        $builder = new RendererBuilder($ak, $view, $this);
        return $builder;
    }

    public function render($ak)
    {
        $ak = $this->getKey($ak);
        $view = $this->getView($ak);
        $this->renderView($view, $ak);
    }

    /**
     * @private
     */
    public function renderView(ViewInterface $view, $ak)
    {
        $value = null;
        if (is_object($this->object)) {
            $value = $this->object->getAttributeValueObject($ak);
        }

        /**
         * @var $view View
         */
        $view->setValue($value);
        $renderer = $view->getControlRenderer();
        $renderer->render();
    }

}
