<?php
namespace Concrete\Core\Express\Form\Control\Renderer;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\AbstractContext;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\RendererFactory;

class AttributeKeyControlRenderer extends AbstractControlRenderer
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

            $template = new Template('attribute_key');

            $av = null;
            if (is_object($entry)) {
                $av = $entry->getAttributeValueObject($ak);
            }
            $view = new EntityPropertyControlView($context);
            $view->addScopeItem('key', $ak);
            $view->addScopeItem('value', $av);

            return $view->render($control, $context->getTemplateFile($template));
        }
    }
}
