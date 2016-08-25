<?php
namespace Concrete\Core\Express\Form\Control\Form;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\AbstractContext;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;

class AttributeKeyControlFormRenderer implements RendererInterface
{
    /**
     * @param ContextInterface $context
     * @param AttributeKeyControl $control
     * @return string
     */
    public function render(ContextInterface $context, Control $control, Entry $entry = null)
    {
        $ak = $control->getAttributeKey();
        if (is_object($ak)) {
            $template = $context->getApplication()->make('environment')->getPath(
                DIRNAME_ELEMENTS .
                '/' . DIRNAME_EXPRESS .
                '/' . DIRNAME_EXPRESS_FORM_CONTROLS .
                '/attribute_key.php'
            );

            $av = null;
            if (is_object($entry)) {
                $av = $entry->getAttributeValueObject($ak);
            }
            $view = new EntityPropertyControlView($context);
            $view->addScopeItem('key', $ak);
            $view->addScopeItem('value', $av);

            return $view->render($control, $template);
        }
    }
}
