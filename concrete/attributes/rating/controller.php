<?php
namespace Concrete\Attribute\Rating;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;

class Controller extends AttributeTypeController implements SimpleTextExportableAttributeInterface
{
    public $helpers = ['rating'];

    protected $searchIndexFieldDefinition = [
        'type' => 'decimal',
        'options' => ['precision' => 14, 'scale' => 4, 'default' => 0, 'notnull' => false],
    ];

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('star');
    }

    public function getAttributeValueClass()
    {
        return NumberValue::class;
    }

    public function getDisplayValue()
    {
        $rt = $this->app->make('helper/rating');

        return $rt->outputDisplay($this->attributeValue->getValue());
    }

    public function form()
    {
        $caValue = 0;
        if (is_object($this->attributeValue)) {
            $caValue = $this->attributeValue->getValue() / 20;
        }
        $this->set('value', $caValue);
    }

    public function searchForm($list)
    {
        $minRating = $this->request('value');
        $minRating = $minRating * 20;
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $minRating, '>=');

        return $list;
    }

    public function createAttributeValue($rating)
    {
        $value = new NumberValue();
        if ('' == $rating) {
            $rating = 0;
        }
        $value->setValue($rating);

        return $value;
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();

        return $this->createAttributeValue($data['value'] * 20);
    }

    public function search()
    {
        $rt = $this->app->make('helper/form/rating');
        echo $rt->rating($this->field('value'), $this->request('value'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::getAttributeValueTextRepresentation()
     */
    public function getAttributeValueTextRepresentation()
    {
        $result = '';
        $value = $this->getAttributeValueObject();
        if (null !== $value) {
            $number = $value->getValue();
            if (null !== $number) {
                $result = (string) (int) round($number / 20);
            }
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
        } else {
            $i = filter_var($textRepresentation, FILTER_VALIDATE_INT);
            if (null === $i) {
                $warnings->add(t('"%1$s" is not a valid rating value for the attribute with handle %2$s', $textRepresentation, $this->attributeKey->getAttributeKeyHandle()));
            } elseif ($i < 0 || $i > 5) {
                $warnings->add(t('The rating value of the attribute with handle %2$s must range from 0 to 5 (value received: %1$s)', $textRepresentation, $this->attributeKey->getAttributeKeyHandle()));
            } else {
                $i = $i * 20;
                if (null === $value) {
                    $value = $this->createAttributeValue($i);
                } else {
                    $value->setValue($i);
                }
            }
        }

        return $value;
    }

    public function type_form()
    {
        $this->set('form', \Core::make('helper/form'));
        $this->set('akIsRequired', $this->getAttributeKey() ? $this->getAttributeKey()->getAkIsRequired() : false);
    }

    public function validateForm($data)
    {
        $required = $this->getAttributeKey()->getAkIsRequired();
        $value = $data['value']->getValue();

        if (!$required) {
            return true;
        } elseif ($required && !$value) {
            return new Error(t('You must specify a valid a rating for attribute %s', $this->getAttributeKey()
                ->getAttributeKeyDisplayName()),
                new AttributeField($this->getAttributeKey())
            );
        }

        return true;
    }
}
