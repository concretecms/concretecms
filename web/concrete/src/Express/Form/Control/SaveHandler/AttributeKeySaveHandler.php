<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\BaseEntity;
use Concrete\Core\Express\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class AttributeKeySaveHandler implements SaveHandlerInterface
{
    public function saveFromRequest(ObjectManager $manager, Control $control, BaseEntity $entity, Request $request)
    {
        $controller = $control->getAttributeKey()->getController();
        $controller->setAttributeKey($control->getAttributeKey());
        $data = $controller->post();
        $value = $controller->saveForm($data);
        $manager->setAttribute($entity, $control->getAttributeKey(), $value);
    }
}
