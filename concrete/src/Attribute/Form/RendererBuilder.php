<?php
namespace Concrete\Core\Attribute\Form;

use Concrete\Core\Attribute\Form\Control\View\View;
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
        $this->view->setLabel($key->getAttributeKeyDisplayName());
    }

    public function __call($name, $arguments)
    {
        call_user_func_array([$this->view, $name], $arguments);
        return $this;
    }

    public function render()
    {
        $this->renderer->renderView($this->view, $this->key);
    }

}
