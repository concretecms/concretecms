<?php

namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Key\Type\TextType;
use Concrete\Core\Entity\Attribute\Value\TextValue;
use Concrete\Core\Entity\AttributeValue\TextAttributeValue;
use Core;
use Database;
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
        print Core::make('helper/form')->textarea($this->field('value'), $value);
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

    public function getDisplaySanitizedValue()
    {
        return Core::make('helper/text')->entities($this->getValue());
    }

    public function search()
    {
        $f = Core::make('helper/form');
        print $f->text($this->field('value'), $this->request('value'));
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function saveValue($value)
    {
        $av = new TextValue();
        $av->setValue($value);
        return $av;
    }

    public function importKey($akey)
    {
        $type = new TextType();
        return $type;
    }

    public function saveKey($data)
    {
        $type = new TextType();
        return $type;
    }

    public function saveForm($data)
    {
        return $this->saveValue(isset($data['value']) ? $data['value'] : null);
    }

    public function deleteKey()
    {
        $db = Database::get();
        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute('delete from atDefault where avID = ?', array($id));
        }
    }

    public function validateValue()
    {
        return $this->getValue() != '';
    }

    public function validateForm($data)
    {
        return $data['value'] != '';
    }

    public function deleteValue()
    {
        $db = Database::get();
        $db->Execute('delete from atDefault where avID = ?', array($this->getAttributeValueID()));
    }
}
