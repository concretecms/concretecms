<?php
namespace Concrete\Core\Attribute\Form;

use Concrete\Core\Attribute\Context\ComposerContext;
use Concrete\Core\Attribute\Context\ContextInterface;
use Concrete\Core\Attribute\Context\FormContextInterface;
use Concrete\Core\Attribute\Form\Control\View;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Entity\Attribute\Key\Key;

class Renderer
{

    protected $context;
    protected $object;

    public function __construct(FormContextInterface $context, ObjectInterface $object = null)
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
     * @return FormContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param FormContextInterface $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return ObjectInterface
     */
    public function getAttributeObject()
    {
        return $this->object;
    }

    /**
     * @param ObjectInterface $object
     */
    public function setAttributeObject($object)
    {
        $this->object = $object;
    }


    protected function getView($ak)
    {
        /**
         * @var $key Key
         * @var $view View
         */
        $key = $this->getKey($ak);
        $view = $key->getFormControlView($this->context);
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
        $value = null;
        if (is_object($this->object)) {
            $value = $this->object->getAttributeValueObject($ak);
        }
        $view->render($ak, $value);

    }

}
