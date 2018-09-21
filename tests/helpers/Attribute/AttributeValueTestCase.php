<?php

namespace Concrete\TestHelpers\Attribute;

use Closure;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Entity\Attribute\Key\PageKey as PageKeyEntity;
use Concrete\Core\Entity\Attribute\Value\PageValue as PageAttributeValueEntity;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Core;
use Page;

abstract class AttributeValueTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = [];
    protected $metadatas = [
        'Concrete\Core\Entity\Site\Site',
        'Concrete\Core\Entity\Site\Type',
        'Concrete\Core\Entity\Site\Locale',
        'Concrete\Core\Entity\Site\Tree',
        'Concrete\Core\Entity\Site\SiteTree',
        'Concrete\Core\Entity\Attribute\Category',
        'Concrete\Core\Entity\Attribute\Type',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\PageKey',
        'Concrete\Core\Entity\Attribute\Value\Value',
        'Concrete\Core\Entity\Attribute\Value\Value\Value',
        'Concrete\Core\Entity\Attribute\Value\PageValue',
        'Concrete\Core\Entity\Page\PagePath',
    ];

    protected $tables = [
        'Collections',
        'CollectionVersions',
        'Pages',
        'PageThemes',
        'Groups',
    ];

    protected function setUp()
    {
        $service = Core::make('site/type');
        if (!$service->getDefault()) {
            $service->installDefault();
        }

        $service = Core::make('site');
        if (!$service->getDefault()) {
            $service->installDefault('en_US');
        }

        $this->category = AttributeKeyCategory::add('collection');
        $this->object = Page::addHomePage();
        $type = AttributeType::add($this->getAttributeTypeHandle(), $this->getAttributeTypeName());

        $key = new PageKeyEntity();
        $key->setAttributeKeyName($this->getAttributeKeyName());
        $key->setAttributeKeyHandle($this->getAttributeKeyHandle());

        CollectionKey::add($type, $key, $this->createAttributeKeySettings());

        parent::setUp();
    }

    public function tearDown()
    {
        // Attribute tests need tables truncated after every test
        $this->truncateTables();

        parent::tearDown();
    }

    abstract public function getAttributeTypeHandle();

    abstract public function getAttributeTypeName();

    abstract public function getAttributeKeyHandle();

    abstract public function getAttributeKeyName();

    abstract public function createAttributeKeySettings();

    abstract public function getAttributeValueClassName();

    /**
     *  @dataProvider baseAttributeValues
     *
     * @param mixed $input
     * @param mixed $expectedBaseValue
     */
    public function testBaseAttributeValueGet($input, $expectedBaseValue)
    {
        // because of stupid !@#!@ phpunit.
        if ($input instanceof Closure) {
            $input = $input();
        }
        if ($expectedBaseValue instanceof Closure) {
            $expectedBaseValue = $expectedBaseValue();
        }

        $this->object->setAttribute($this->getAttributeKeyHandle(), $input);
        $baseValue = $this->object->getAttribute($this->getAttributeKeyHandle());
        $baseValue = $this->prepareBaseValueAfterRetrieving($baseValue);
        $this->assertEquals($expectedBaseValue, $baseValue);

        $value = $this->object->getAttributeValueObject($this->getAttributeKeyHandle());
        $this->assertInstanceOf(PageAttributeValueEntity::class, $value);

        $this->assertInstanceOf($this->getAttributeValueClassName(), $value->getValueObject());
    }

    /**
     *  @dataProvider displayAttributeValues
     *
     * @param mixed $input
     * @param mixed $expectedDisplayValue
     */
    public function testDisplayAttributeValues($input, $expectedDisplayValue)
    {
        // because of stupid !@#!@ phpunit.
        if ($input instanceof Closure) {
            $input = $input();
        }
        if ($expectedDisplayValue instanceof Closure) {
            $expectedDisplayValue = $expectedDisplayValue();
        }

        $this->object->setAttribute($this->getAttributeKeyHandle(), $input);
        $displayValue1 = $this->object->getAttribute($this->getAttributeKeyHandle(), 'display');
        $displayValue2 = $this->object->getAttribute($this->getAttributeKeyHandle(), 'displaySanitized');

        $value = $this->object->getAttributeValueObject($this->getAttributeKeyHandle());

        $displayValue3 = $value->getDisplayValue();
        $displayValue4 = $value->getDisplaySanitizedValue();
        $displayValue5 = (string) $value;

        $this->assertEquals($displayValue1, $displayValue2);
        $this->assertEquals($displayValue2, $displayValue3);
        $this->assertEquals($displayValue3, $displayValue4);
        $this->assertEquals($displayValue4, $displayValue5);

        $this->assertEquals($expectedDisplayValue, $displayValue1);
    }

    /**
     *  @dataProvider plaintextAttributeValues
     *
     * @param mixed $input
     * @param mixed $expectedPlainTextOutput
     */
    public function testPlainTextAttributeValues($input, $expectedPlainTextOutput)
    {
        // because of stupid !@#!@ phpunit.
        if ($input instanceof Closure) {
            $input = $input();
        }
        if ($expectedPlainTextOutput instanceof Closure) {
            $expectedPlainTextOutput = $expectedPlainTextOutput();
        }

        $this->object->setAttribute($this->getAttributeKeyHandle(), $input);
        $value = $this->object->getAttributeValueObject($this->getAttributeKeyHandle());
        $plainTextValue = $value->getPlainTextValue();

        $this->assertEquals($expectedPlainTextOutput, $plainTextValue);
    }

    /**
     *  @dataProvider searchIndexAttributeValues
     *
     * @param mixed $input
     * @param mixed $expectedSearchIndexValue
     */
    public function testSearchIndexAttributeValues($input, $expectedSearchIndexValue)
    {
        // because of stupid !@#!@ phpunit.
        if ($input instanceof Closure) {
            $input = $input();
        }
        if ($expectedSearchIndexValue instanceof Closure) {
            $expectedSearchIndexValue = $expectedSearchIndexValue();
        }

        $this->object->setAttribute($this->getAttributeKeyHandle(), $input);
        $value = $this->object->getAttributeValueObject($this->getAttributeKeyHandle());
        $searchIndexValue = $value->getSearchIndexValue();

        $this->assertEquals($expectedSearchIndexValue, $searchIndexValue);
    }

    protected function prepareBaseValueAfterRetrieving($value)
    {
        return $value;
    }
}
