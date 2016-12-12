<?php
namespace Concrete\Attribute\Rating;

use Loader;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{

    protected $searchIndexFieldDefinition = array(
        'type' => 'decimal',
        'options' => array('precision' => 14, 'scale' => 4, 'default' => 0, 'notnull' => false)
    );

    public function getValue()
    {
        $db = Loader::db();
        $value = $db->GetOne("select value from atNumber where avID = ?", array($this->getAttributeValueID()));
        return round($value);
    }


    public function getDisplayValue()
    {
        $value = $this->getValue();
        $rt = Loader::helper('rating');
        return $rt->outputDisplay($value);
    }

    public function form()
    {
        $caValue = 0;
        if ($this->getAttributeValueID() > 0) {
            $caValue = $this->getValue() / 20;
        }
        $rt = Loader::helper('form/rating');
        print $rt->rating($this->field('value'), $caValue);
    }

    public function searchForm($list)
    {
        $minRating = $this->request('value');
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $minRating, '>=');
        return $list;
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function saveValue($rating)
    {
        if ($rating == '') {
            $rating = 0;
        }
        $db = Loader::db();
        $db->Replace('atNumber', array('avID' => $this->getAttributeValueID(), 'value' => $rating), 'avID', true);
    }

    public function deleteKey()
    {
        $db = Loader::db();
        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute('delete from atNumber where avID = ?', array($id));
        }
    }

    public function saveForm($data)
    {
        $this->saveValue($data['value'] * 20);
    }

    public function search()
    {
        $rt = Loader::helper('form/rating');
        print $rt->rating($this->field('value'), $this->request('value'));
    }

    public function deleteValue()
    {
        $db = Loader::db();
        $db->Execute('delete from atNumber where avID = ?', array($this->getAttributeValueID()));
    }

}