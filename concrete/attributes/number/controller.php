<?php

namespace Concrete\Attribute\Number;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Error\ErrorList\ErrorList;

class Controller extends AttributeTypeController implements SimpleTextExportableAttributeInterface
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
        return (float) ($this->attributeValue->getValue());
    }

    public function getAttributeValueClass()
    {
        return NumberValue::class;
    }

    public function searchForm($list)
    {
        $numFrom = (int) ($this->request('from'));
        $numTo = (int) ($this->request('to'));
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::getAttributeValueTextRepresentation()
     */
    public function getAttributeValueTextRepresentation()
    {
        $value = $this->getAttributeValueObject();
        if ($value === null) {
            $result = '';
        } else {
            $result = (string) $value->getValue();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::updateAttributeValueFromTextRepresentation()
     */
    public function updateAttributeValueFromTextRepresentation($textRepresentation, ErrorList $warnings)
    {
        $value = $this->getAttributeValueObject();
        $textRepresentation = trim($textRepresentation);
        if ($textRepresentation === '') {
            if ($value !== null) {
                $value->setValue(null);
            }
        } elseif (is_numeric($textRepresentation)) {
            if ($value === null) {
                $value = $this->createAttributeValue($textRepresentation);
            } else {
                $value->setValue($textRepresentation);
            }
        } else {
            $warnings->add(t('"%1$s" is not a valid number for the attribute with handle %2$s', $textRepresentation, $this->attributeKey->getAttributeKeyHandle()));
        }

        return $value;
    }
}
