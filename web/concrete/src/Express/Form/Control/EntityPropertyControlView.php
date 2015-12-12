<?php

namespace Concrete\Core\Express\Form\Control;

use Concrete\Core\Application\Application;
use Concrete\Core\Express\Form\RendererFactory;

class EntityPropertyControlView implements ControlViewInterface
{

    protected $factory;
    protected $scopeItems = array();

    public function __construct(RendererFactory $factory)
    {
        $this->factory = $factory;
    }

    public function addScopeItem($key, $value)
    {
        $this->scopeItems[$key] = $value;
    }

    public function field($name)
    {
        return 'ccm_express[' . $name . ']';
    }

    public function render($template)
    {
        extract($this->scopeItems);

        $view = $this;
        $form = $this->factory->getApplication()->make('helper/form');
        $control = $this->factory->getControl();
        $label = $control->getDisplayLabel();

        ob_start();
        include($template);
        return ob_get_clean();
    }





}