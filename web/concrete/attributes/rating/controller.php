<?php
namespace Concrete\Attribute\Rating;

use Concrete\Core\Entity\Attribute\Key\Type\RatingType;
use Concrete\Core\Entity\Attribute\Value\RatingValue;
use Loader;
use Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{
    protected $searchIndexFieldDefinition = array(
        'type' => 'decimal',
        'options' => array('precision' => 14, 'scale' => 4, 'default' => 0, 'notnull' => false),
    );

    public function getDisplayValue()
    {
        $value = $this->getValue() / 20;
        $rt = Loader::helper('rating');

        return $rt->output($this->attributeKey->getAttributeKeyHandle() . time(), $value);
    }

    public function importKey($akey)
    {
        $type = new RatingType();

        return $type;
    }

    public function saveKey($data)
    {
        $type = new RatingType();

        return $type;
    }

    public function form()
    {
        $caValue = 0;
        if ($this->getAttributeValueID() > 0) {
            $caValue = $this->getValue() / 20;
        }
        $rt = Loader::helper('form/rating');
        echo $rt->rating($this->field('value'), $caValue);
    }

    public function searchForm($list)
    {
        $minRating = $this->request('value');
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $minRating, '>=');

        return $list;
    }

    public function saveValue($rating)
    {
        $value = new RatingValue();
        if ($rating == '') {
            $rating = 0;
        }
        $value->setValue($rating);

        return $value;
    }

    public function saveForm($data)
    {
        $this->saveValue($data['value'] * 20);
    }

    public function search()
    {
        $rt = Loader::helper('form/rating');
        echo $rt->rating($this->field('value'), $this->request('value'));
    }

    public function createAttributeKeyType()
    {
        return new RatingType();
    }
}
