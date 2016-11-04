<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Key\Settings\TextSettings;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Core;
use Concrete\Core\Attribute\Controller as AttributeTypeController;

class DefaultController extends AttributeTypeController
{
    protected $searchIndexFieldDefinition = array(
        'type' => 'string',
        'options' => array('default' => null, 'notnull' => false),
    );

    public function form()
    {
        $value = '';
        if (is_object($this->attributeValue)) {
            $value = Core::make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        echo Core::make('helper/form')->textarea($this->field('value'), $value);
    }

    public function searchForm($list)
    {
        if ($this->request('value') === '') {
            return $list;
        }
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $this->request('value') . '%',
            'like');

        return $list;
    }

    public function getDisplayValue()
    {
        return Core::make('helper/text')->entities($this->getValue());
    }

    public function getAttributeValueObject()
    {
        return $this->entityManager->find(TextValue::class, $this->attributeValue->getGenericValue());
    }

    public function search()
    {
        $f = Core::make('helper/form');
        echo $f->text($this->field('value'), $this->request('value'));
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function createAttributeValue($value)
    {
        $av = new TextValue();
        $av->setValue($value);

        return $av;
    }

    public function createAttributeKeySettings()
    {
        return new TextSettings();
    }

    protected function retrieveAttributeKeySettings()
    {
        return $this->entityManager->find(TextSettings::class, $this->attributeKey);
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        return $this->createAttributeValue(isset($data['value']) ? $data['value'] : null);
    }

    public function validateValue()
    {
        return $this->getValue() != '';
    }

    public function validateForm($data)
    {
        return isset($data['value']) && $data['value'] != '';
    }
}
