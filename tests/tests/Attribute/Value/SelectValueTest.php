<?php

use Concrete\Core\Entity\Attribute\Value\Value\SelectValue;

class SelectValueTest extends \AttributeValueTestCase
{

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->metadatas = array_merge($this->metadatas, array(
            'Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectValue',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectValueOptionList',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption',
        ));
    }

    protected function setUp()
    {
        \Database::query('truncate atSelectOptionsSelected');
        parent::setUp();
    }

    public function getAttributeKeyHandle()
    {
        return 'operating_systems';
    }

    public function getAttributeKeyName()
    {
        return 'OS';
    }

    public function createAttributeKeySettings()
    {
        $settings = new \Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings();
        $settings->setAllowMultipleValues(true);
        $list = new \Concrete\Core\Entity\Attribute\Value\Value\SelectValueOptionList();

        foreach(['OS X', 'Windows', 'Linux', 'Other'] as $name) {
            $option = new \Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption();
            $option->setSelectAttributeOptionValue($name);
            $option->setOptionList($list);
            $list->getOptions()->add($option);
        }

        $settings->setOptionList($list);
        return $settings;
    }

    public function getAttributeTypeHandle()
    {
        return 'select';
    }

    public function getAttributeTypeName()
    {
        return 'Select';
    }

    public function getAttributeValueClassName()
    {
        return SelectValue::class;
    }

    protected function prepareBaseValueAfterRetrieving($value)
    {
        if (count($value->getSelectedOptions()) > 1) {
            return $value->getSelectedOptions()->toArray();
        } else {
            return $value;
        }
    }

    public function baseAttributeValues()
    {
        return array(
            array(
                'OS X',
                'OS X'
            ),
            array(
                array('Windows', 'OS X'),
                array('Windows', 'OS X')
            ),
            array(
                array('Linux', 'Unix'),
                'Linux'
            )
        );
    }

    public function displayAttributeValues()
    {
        return array(
            array(
                'OS X',
                'OS X<br/>'
            ),
            array(
                array('Windows', 'OS X'),
                'Windows<br/>OS X<br/>',
            ),
            array(
                array('Linux', 'Unix'),
                'Linux<br/>'
            )
        );
    }

    public function plaintextAttributeValues()
    {
        return array(
            array(
                'OS X',
                "OS X"
            ),
            array(
                array('Windows', 'OS X'),
                "Windows\nOS X",
            ),
            array(
                array('Linux', 'Unix'),
                "Linux"
            )
        );
    }

    public function searchIndexAttributeValues()
    {
        return array(
            array(
                'OS X',
                "\nOS X\n"
            ),
            array(
                array('Windows', 'OS X'),
                "\nWindows\nOS X\n",
            ),
            array(
                array('Linux', 'Unix'),
                "\nLinux\n"
            )
        );
    }



}
