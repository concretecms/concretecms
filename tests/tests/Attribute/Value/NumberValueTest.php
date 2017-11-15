<?php

namespace Concrete\Tests\Attribute\Value;

use Concrete\TestHelpers\Attribute\AttributeValueTestCase;

class NumberValueTest extends AttributeValueTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, []);

        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
        ]);
    }

    public function getAttributeKeyHandle()
    {
        return 'test_number';
    }

    public function getAttributeKeyName()
    {
        return 'Number';
    }

    public function createAttributeKeySettings()
    {
        return null;
    }

    public function getAttributeTypeHandle()
    {
        return 'number';
    }

    public function getAttributeTypeName()
    {
        return 'Number';
    }

    public function getAttributeValueClassName()
    {
        return 'Concrete\Core\Entity\Attribute\Value\Value\NumberValue';
    }

    public function baseAttributeValues()
    {
        return [
            [
                5,
                5,
            ],
            [
                12.5,
                12.5,
            ],
            [
                12.505,
                12.505,
            ],
        ];
    }

    public function displayAttributeValues()
    {
        return [
            [
                5,
                5,
            ],
        ];
    }

    public function plaintextAttributeValues()
    {
        return [
            [
                5,
                5,
            ],
        ];
    }

    public function searchIndexAttributeValues()
    {
        return [
            [
                5,
                5,
            ],
        ];
    }
}
