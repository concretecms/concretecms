<?php
namespace Concrete\Attribute\UserGroup;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Form\Service\Widget\GroupSelector;
use Concrete\Core\User\Group\Group;
use SimpleXMLElement;

class Controller extends AttributeTypeController
{
    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('users');
    }

    public function form()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
        }
        if (!$value) {
            if ($this->request->query->has($this->attributeKey->getAttributeKeyHandle())) {
                $value = $this->createAttributeValue((int) $this->request->query->get($this->attributeKey->getAttributeKeyHandle()));
            }
        }
        $this->set('value', $value);
        $this->set('selector', $this->app->make(GroupSelector::class));
    }

    public function getAttributeValueClass()
    {
        return NumberValue::class;
    }

    public function getDisplayValue()
    {
        /** @var \Concrete\Core\User\Group\Group $group */
        $group = $this->getGroup($this->getAttributeValue()->getValue());

        //TODO Check what would be the correct response in this case.
        if ($group) {
            return $group->getGroupName();
        }
        return t('None');
    }

    public function getPlainTextValue()
    {
        /** @var \Concrete\Core\User\Group\Group $group */
        $group = $this->getGroup($this->getAttributeValue()->getValue());
        if ($group) {
            return $group->getGroupName();
        }
        return '';
    }

    public function createAttributeValue($value)
    {
        $av = new NumberValue();
        if ($value instanceof Group) {
            $value = $value->getGroupID();
        }
        $av->setValue($value);

        return $av;
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        if (isset($data['value'])) {
            return $this->createAttributeValue((int) $data['value']);
        }
    }

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            $group = Group::getByPath($akv->value);
            if (is_object($group)) {
                return $group->getGroupID();
            }
        }
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        if (is_object($this->attributeValue)) {
            $gID = $this->getAttributeValue()->getValue();
            $group = Group::getByID($gID);
            $avn = $akn->addChild('value', $group->getGroupPath());
        }
    }

    private function getGroup($id)
    {
        $group = Group::getByID($id);

        if (!is_object($group)) {
            //TODO Return group not found error/exception?
            return null;
        }

        return $group;
    }

}
