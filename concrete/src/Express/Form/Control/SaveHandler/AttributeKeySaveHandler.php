<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class AttributeKeySaveHandler implements SaveHandlerInterface
{
    public function saveFromRequest(Control $control, Entry $entry, Request $request)
    {
        if (!$control instanceof AttributeKeyControl) {
            throw new \InvalidArgumentException(t('The control must be an instance of %s.', AttributeKeyControl::class));
        }
        $controller = $control->getAttributeKey()->getController();
        $controller->setAttributeKey($control->getAttributeKey());
        $controller->setAttributeObject($entry);
        $value = $controller->createAttributeValueFromRequest();
        $entry->setAttribute($control->getAttributeKey(), $value);
    }
}
