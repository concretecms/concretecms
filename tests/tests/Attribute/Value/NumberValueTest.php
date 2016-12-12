<?php

class NumberValueTest extends \AttributeValueTestCase
{

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array());

        $this->metadatas = array_merge($this->metadatas, array(
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
        ));
        parent::setUp();
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
        return array(
            array(
                5,
                5,
            ),
            array(
                12.5,
                12.5
            ),
            array(
                12.505,
                12.505
            )
        );
    }

    public function displayAttributeValues()
    {
        return array(
            array(
                5,
                5
            )
        );
    }

    public function plaintextAttributeValues()
    {
        return array(
            array(
                5,
                5,
            )
        );
    }

    public function searchIndexAttributeValues()
    {
        return array(
            array(
                5,
                5,
            )
        );
    }



}
