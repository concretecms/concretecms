<?php

namespace Concrete\Tests\Attribute\Value;

use Concrete\TestHelpers\Attribute\AttributeValueTestCase;

class NumberValueTest extends AttributeValueTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
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

    public static function baseAttributeValues()
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

    public static function displayAttributeValues()
    {
        return [
            [
                5,
                5,
            ],
        ];
    }

    public static function plaintextAttributeValues()
    {
        return [
            [
                5,
                5,
            ],
        ];
    }

    public static function searchIndexAttributeValues()
    {
        return [
            [
                5,
                5,
            ],
        ];
    }
}
