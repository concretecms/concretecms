<?php

namespace Concrete\Tests\Attribute\Value;

use Concrete\Core\Entity\Attribute\Value\Value\SelectValue;
use Concrete\TestHelpers\Attribute\AttributeValueTestCase;

class SelectValueTest extends AttributeValueTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectValue',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectValueOptionList',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption',
        ]);
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

        foreach (['OS X', 'Windows', 'Linux', 'Other'] as $name) {
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

    public function baseAttributeValues()
    {
        return [
            [
                'OS X',
                'OS X',
            ],
            [
                ['Windows', 'OS X'],
                ['Windows', 'OS X'],
            ],
            [
                ['Linux', 'Unix'],
                'Linux',
            ],
        ];
    }

    public function displayAttributeValues()
    {
        return [
            [
                'OS X',
                'OS X<br/>',
            ],
            [
                ['Windows', 'OS X'],
                'Windows<br/>OS X<br/>',
            ],
            [
                ['Linux', 'Unix'],
                'Linux<br/>',
            ],
        ];
    }

    public function plaintextAttributeValues()
    {
        return [
            [
                'OS X',
                'OS X',
            ],
            [
                ['Windows', 'OS X'],
                "Windows\nOS X",
            ],
            [
                ['Linux', 'Unix'],
                'Linux',
            ],
        ];
    }

    public function searchIndexAttributeValues()
    {
        return [
            [
                'OS X',
                "\nOS X\n",
            ],
            [
                ['Windows', 'OS X'],
                "\nWindows\nOS X\n",
            ],
            [
                ['Linux', 'Unix'],
                "\nLinux\n",
            ],
        ];
    }

    protected function prepareBaseValueAfterRetrieving($value)
    {
        if (count($value->getSelectedOptions()) > 1) {
            return $value->getSelectedOptions()->toArray();
        } else {
            return $value;
        }
    }
}
