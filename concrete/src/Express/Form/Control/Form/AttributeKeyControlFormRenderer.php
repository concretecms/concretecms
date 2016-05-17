<?php
namespace Concrete\Core\Express\Form\Control\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;

class AttributeKeyControlFormRenderer implements RendererInterface
{
    protected $factory;
    protected $entry;

    public function __construct(Entry $entry = null)
    {
        $this->entry = $entry;
    }

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

            $av = null;
            if (is_object($this->entry)) {
                $av = $this->entry->getAttributeValueObject($ak);
            }
            $view = new EntityPropertyControlView($this->factory);
            $view->addScopeItem('key', $ak);
            $view->addScopeItem('value', $av);

            return $view->render($template);
        }
    }
}
