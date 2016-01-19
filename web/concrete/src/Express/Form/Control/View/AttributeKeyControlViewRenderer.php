<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Express\BaseEntity;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;

class AttributeKeyControlViewRenderer implements RendererInterface
{
    protected $factory;
    protected $entity;

    public function __construct(BaseEntity $entity)
    {
        $this->entity = $entity;
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
                '/' . DIRNAME_EXPRESS_VIEW_CONTROLS .
                '/attribute_key.php'
            );

            $av = $this->entity->getAttributeValueObject($ak);
            $view = new EntityPropertyControlView($this->factory);
            $view->addScopeItem('value', $av);

            return $view->render($template);
        }
    }
}
