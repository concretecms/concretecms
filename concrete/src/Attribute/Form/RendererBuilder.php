<?php
namespace Concrete\Core\Attribute\Form;

use Concrete\Core\Attribute\Form\Control\View;
use Concrete\Core\Entity\Attribute\Key\Key;

class RendererBuilder
{

    protected $view;
    protected $renderer;
    protected $key;

    public function __construct(Key $key, View $view, Renderer $renderer)
    {
        $this->view = $view;
        $this->renderer = $renderer;
        $this->key = $key;
    }

    public function __call($name, $arguments)
    {
        call_user_func_array([$this->view, $name], $arguments);
        return $this;
    }

    public function render()
    {
        $object = $this->renderer->getAttributeObject();
        $value = null;
        if (is_object($object)) {
            $value = $object->getAttributeValueObject($this->key);
        }
        $this->view->render($this->key, $value);
    }

}
