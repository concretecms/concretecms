<?php
namespace Concrete\Core\Attribute;

use Loader;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Attribute\Controller as AttributeTypeController;

class DefaultController extends AttributeTypeController
{

    protected $searchIndexFieldDefinition = array(
        'type' => 'string',
        'options' => array('default' => null, 'notnull' => false)
    );

    public function getValue()
    {
        $db = Loader::db();
        $value = $db->GetOne("select value from atDefault where avID = ?", array($this->getAttributeValueID()));
        return $value;
    }

    public function form()
    {
        if (is_object($this->attributeValue)) {
            $value = Loader::helper('text')->entities($this->getAttributeValue()->getValue());
        }
        print Loader::helper('form')->textarea($this->field('value'), $value);
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
        return Loader::helper('text')->entities($this->getValue());
    }

    public function search()
    {
        $f = Loader::helper('form');
        print $f->text($this->field('value'), $this->request('value'));
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function saveValue($value)
    {
        $db = Loader::db();
        $db->Replace('atDefault', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
    }

    public function saveForm($data)
    {
        $this->saveValue($data['value']);
    }

    public function deleteKey()
    {
        $db = Loader::db();
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
        $db = Loader::db();
        $db->Execute('delete from atDefault where avID = ?', array($this->getAttributeValueID()));
    }

}
