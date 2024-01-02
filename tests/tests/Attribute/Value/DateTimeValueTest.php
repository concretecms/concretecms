<?php
namespace Concrete\Tests\Attribute\Value;

use Concrete\Core\Entity\Attribute\Value\Value\DateTimeValue;
use Concrete\TestHelpers\Attribute\AttributeValueTestCase;
use DateTime;

class DateTimeValueTest extends AttributeValueTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, []);

        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings',
            'Concrete\Core\Entity\Attribute\Value\Value\DateTimeValue',
        ]);
    }

    public function getAttributeKeyHandle()
    {
        return 'datetime';
    }

    public function getAttributeKeyName()
    {
        return 'DateTime';
    }

    public function createAttributeKeySettings()
    {
        return null;
    }

    public function getAttributeTypeHandle()
    {
        return 'date_time';
    }

    public function getAttributeTypeName()
    {
        return 'Date/Time';
    }

    public function getAttributeValueClassName()
    {
        return DateTimeValue::class;
    }

    public function baseAttributeValues()
    {
        return [
            [
                $this->getDateTime1(),
                $this->getDateTime1(),
            ],
        ];
    }

    public function displayAttributeValues()
    {
        return [
            [
                $this->getDateTime1(),
                '1/1/24, 12:12 PM',
            ],
        ];
    }

    public function plaintextAttributeValues()
    {
        return [
            [
                $this->getDateTime1(),
                "2024-01-01T12:12:12+00:00",
            ],
        ];
    }

    public function searchIndexAttributeValues()
    {
        return [
            [
            ],
        ];
    }

    /**
     * Tests validation through the controller.
     */
    public function testControllerFormValidation()
    {
        $ctrl = $this->getControllerWithValue();
        return;
    }

    public function testSearchIndexAttributeValues($input = null, $expectedSearchIndexValue = null) {
        return;
    }

    protected function getControllerWithValue()
    {
        $this->object->setAttribute(
            $this->getAttributeKeyHandle(),
            $this->getDateTime1(false)
        );
        $val = $this->object->getAttributeValueObject($this->getAttributeKeyHandle());
        $ctrl = $val->getController();
        $ctrl->setAttributeValue($val);

        return $ctrl;
    }

    protected function prepareBaseValueAfterRetrieving($value)
    {
        $object = new DateTimeValue();
        $object->setValue($value);
        $object->setGenericValue(null);

        return $object;
    }

    protected function getDateTime1($asObject = true)
    {
        $date = '2024-01-01T12:12:12+00:00';
        if ($asObject) {
            $object = new DateTimeValue();
            $object->setValue(new DateTime($date));

            return $object;
        } else {
            return $date;
        }
    }
}
