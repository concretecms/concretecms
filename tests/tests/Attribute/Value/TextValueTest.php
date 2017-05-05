<?php

class TextValueTest extends \AttributeValueTestCase
{

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, array(
        ));

        $this->metadatas = array_merge($this->metadatas, array(
            'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings',
            'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
        ));
    }

    public function getAttributeKeyHandle()
    {
        return 'meta_keywords';
    }

    public function getAttributeKeyName()
    {
        return 'Meta Keywords';
    }

    public function createAttributeKeySettings()
    {
        return new \Concrete\Core\Entity\Attribute\Key\Settings\TextSettings();
    }

    public function getAttributeTypeHandle()
    {
        return 'text';
    }

    public function getAttributeTypeName()
    {
        return 'text';
    }

    public function getAttributeValueClassName()
    {
        return 'Concrete\Core\Entity\Attribute\Value\Value\TextValue';
    }

    public function baseAttributeValues()
    {
        return array(
            array(
                'This is my fun input',
                'This is my fun input',
            )
        );
    }

    public function displayAttributeValues()
    {
        return array(
            array(
                'This is my fun input',
                'This is my fun input'
            )
        );
    }

    public function plaintextAttributeValues()
    {
        return array(
            array(
                'This is my fun input',
                'This is my fun input'
            )
        );
    }

    public function searchIndexAttributeValues()
    {
        return array(
            array(
                'This is my fun input',
                'This is my fun input'
            )
        );
    }




}
