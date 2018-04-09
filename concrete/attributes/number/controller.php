<?php
namespace Concrete\Attribute\Number;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;

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

    public function validateForm($data)
    {
        $required = $this->getAttributeKey()->getAkIsRequired();

        if (!$required) {
            return true;
        } elseif ($required && !$data['value']->getValue()) {
            return new Error(t('You must specify a valid number for %s', $this->getAttributeKey()->getAttributeKeyDisplayName()),
                new AttributeField($this->getAttributeKey())
            );
        }

        if (!is_numeric($data['value']->getValue())) {
            return new Error(t('You must specify a valid number for %s', $this->getAttributeKey()->getAttributeKeyDisplayName()),
                new AttributeField($this->getAttributeKey())
            );
        }

        return true;
    }

    public function validateValue()
    {
        $val = $this->getAttributeValue()->getValue();

        return null !== $val && false !== $val;
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function createAttributeValue($value)
    {
        $av = new NumberValue();
        $value = (false == $value || '0' == $value) ? 0 : $value;
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
        if (null === $value) {
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
        if ('' === $textRepresentation) {
            if (null !== $value) {
                $value->setValue(null);
            }
        } elseif (is_numeric($textRepresentation)) {
            if (null === $value) {
                $value = $this->createAttributeValue($textRepresentation);
            } else {
                $value->setValue($textRepresentation);
            }
        } else {
            $warnings->add(t('"%1$s" is not a valid number for the attribute with handle %2$s', $textRepresentation, $this->attributeKey->getAttributeKeyHandle()));
        }

        return $value;
    }

    public function type_form()
    {
        $this->set('form', \Core::make('helper/form'));
        $this->set('akIsRequired', $this->getAttributeKey() ? $this->getAttributeKey()->getAkIsRequired() : false);
    }
}
