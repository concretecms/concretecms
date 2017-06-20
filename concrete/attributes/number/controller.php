<?php
namespace Concrete\Attribute\Number;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{
    protected $searchIndexFieldDefinition = [
        'type' => 'decimal',
        'options' => ['precision' => 14, 'scale' => 4, 'default' => 0, 'notnull' => false],
    ];

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('hashtag');
    }

    public function getDisplayValue()
    {
        return floatval($this->attributeValue->getValue());
    }

    public function getAttributeValueClass()
    {
        return NumberValue::class;
    }

    public function searchForm($list)
    {
        $numFrom = intval($this->request('from'));
        $numTo = intval($this->request('to'));
        if ($numFrom) {
            $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $numFrom, '>=');
        }
        if ($numTo) {
            $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $numTo, '<=');
        }

        return $list;
    }

    public function search()
    {
        $f = $this->app->make('helper/form');
        $html = $f->number($this->field('from'), $this->request('from'));
        $html .= ' ' . t('to') . ' ';
        $html .= $f->number($this->field('to'), $this->request('to'));
        echo $html;
    }

    public function form()
    {
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
        } else {
            $value = null;
        }
        $this->set('form', $this->app->make('helper/form'));
        $this->set('value', $value);
    }

    public function validateForm($p)
    {
        return $p['value'] != false;
    }

    public function validateValue()
    {
        $val = $this->getAttributeValue()->getValue();
        return $val !== null && $val !== false;
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function createAttributeValue($value)
    {
        $av = new NumberValue();
        $value = ($value == false || $value == '0') ? 0 : $value;
        $av->setValue($value);

        return $av;
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        if (isset($data['value'])) {
            return $this->createAttributeValue($data['value']);
        }
    }

}
