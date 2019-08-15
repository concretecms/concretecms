<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Entity\Attribute\Key\Settings\TextSettings;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Concrete\Core\Error\ErrorList\ErrorList;
use Core;

class DefaultController extends AttributeTypeController implements SimpleTextExportableAttributeInterface
{
    protected $searchIndexFieldDefinition = [
        'type' => 'text',
        'options' => ['default' => null, 'notnull' => false],
    ];

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

    /**
     * @since 8.0.0
     */
    public function getDisplayValue()
    {
        return Core::make('helper/text')->entities($this->attributeValue->getValue());
    }

    /**
     * @since 8.0.3
     */
    public function getAttributeValueClass()
    {
        return TextValue::class;
    }

    public function search()
    {
        $f = Core::make('helper/form');
        echo $f->text($this->field('value'), $this->request('value'));
    }

    // run when we call setAttribute(), instead of saving through the UI
    /**
     * @since 8.0.0
     */
    public function createAttributeValue($value)
    {
        $av = new TextValue();
        $av->setValue($value);

        return $av;
    }

    /**
     * @since 8.0.3
     */
    public function getAttributeKeySettingsClass()
    {
        return TextSettings::class;
    }

    /**
     * @since 8.0.0
     */
    public function createAttributeValueFromRequest()
    {
        $data = $this->post();

        return $this->createAttributeValue(isset($data['value']) ? $data['value'] : null);
    }

    /**
     * @since 5.7.4.2
     */
    public function validateValue()
    {
        return $this->attributeValue->getValue() != '';
    }

    public function validateForm($data)
    {
        return isset($data['value']) && $data['value'] != '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::getAttributeValueTextRepresentation()
     * @since 8.3.0
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
     * @since 8.3.0
     */
    public function updateAttributeValueFromTextRepresentation($textRepresentation, ErrorList $warnings)
    {
        $value = $this->getAttributeValueObject();
        if ($value === null) {
            if ($textRepresentation !== '') {
                $value = $this->createAttributeValue($textRepresentation);
            }
        } else {
            $value->setValue($textRepresentation);
        }

        return $value;
    }
}
