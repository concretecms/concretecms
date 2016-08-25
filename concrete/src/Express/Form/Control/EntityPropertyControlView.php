<?php
namespace Concrete\Core\Express\Form\Control;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\RendererFactory;

class EntityPropertyControlView implements ControlViewInterface
{
    protected $scopeItems = array();
    protected $context;

    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    public function addScopeItem($key, $value)
    {
        $this->scopeItems[$key] = $value;
    }

    public function field($name)
    {
        return 'ccm_express[' . $name . ']';
    }

    public function render(Control $control, $template)
    {
        extract($this->scopeItems);

        $view = $this;
        $form = $this->context->getApplication()->make('helper/form');
        $label = $control->getDisplayLabel();
        $renderer = $this->context->getFormRenderer();

        ob_start();
        include $template;

        return ob_get_clean();
    }
}
