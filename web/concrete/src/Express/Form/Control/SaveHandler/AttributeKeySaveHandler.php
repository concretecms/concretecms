<?php

namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Express\BaseEntity;
use Concrete\Core\Express\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class AttributeKeySaveHandler implements SaveHandlerInterface
{

    protected $control;

    function __construct(AttributeKeyControl $control)
    {
        $this->control = $control;
    }


    public function saveFromRequest(ObjectManager $manager, BaseEntity $entity, Request $request)
    {
        $controller = $this->control->getAttributeKey()->getController();
        $controller->setAttributeKey($this->control->getAttributeKey());
        $data = $controller->post();
        $value = $controller->saveForm($data);
        $manager->setAttribute($entity, $this->control->getAttributeKey(), $value);
    }

}