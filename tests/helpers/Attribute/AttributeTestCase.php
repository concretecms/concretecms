<?php

namespace Concrete\TestHelpers\Attribute;

use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Core;
use Database;

abstract class AttributeTestCase extends ConcreteDatabaseTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see ConcreteDatabaseTestCase::$fixtures
     */
    protected $fixtures = [];

    /**
     * {@inheritdoc}
     *
     * @see ConcreteDatabaseTestCase::$metadatas
     */
    protected $metadatas = [
        'Concrete\Core\Entity\Site\Site',
        'Concrete\Core\Entity\Site\Locale',
        'Concrete\Core\Entity\Site\Tree',
        'Concrete\Core\Entity\Site\SiteTree',
        'Concrete\Core\Entity\Site\Type',
        'Concrete\Core\Entity\Attribute\Category',
        'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings',
        'Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings',
        'Concrete\Core\Entity\Attribute\Key\Settings\BooleanSettings',
        'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\PageKey',
        'Concrete\Core\Entity\Attribute\Type',
        'Concrete\Core\Entity\Attribute\Value\Value\TextareaValue',
        'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
        'Concrete\Core\Entity\Attribute\Value\Value\BooleanValue',
        'Concrete\Core\Entity\Attribute\Value\Value\Value',
        'Concrete\Core\Entity\Attribute\Value\Value',
        'Concrete\Core\Entity\Attribute\Value\PageValue',
        'Concrete\Core\Entity\Attribute\Key\UserValue',
        'Concrete\Core\Entity\Attribute\Key\UserKey',
    ];

    /**
     * @var \Concrete\Core\Attribute\ObjectInterface|null
     */
    protected $object;

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        // Truncate tables
        $this->truncateTables();

        parent::setUp();
        $service = Core::make('site');
        if (!$service->getDefault()) {
            $service->installDefault('en_US');
        }
        $this->installAttributeCategoryAndObject();
        AttributeType::add('boolean', 'Boolean');
        AttributeType::add('textarea', 'Textarea');
        AttributeType::add('number', 'number');
        AttributeType::add('text', 'text');
        foreach ($this->keys as $akHandle => $args) {
            $args['akHandle'] = $akHandle;
            $type = AttributeType::getByHandle($args['type']);
            $this->keys[] = call_user_func_array([$this->getAttributeKeyClass(), 'add'], [$type, $args]);
        }
    }

    /**
     * Get the test data for the testSetAttribute/testResetAttributes methods.
     *
     * @return array
     */
    abstract public function attributeValues();

    /**
     * Get the test data for the testUnsetAttributes method.
     */
    abstract public function attributeHandles();

    /**
     *  @dataProvider attributeValues
     *
     * @param \Concrete\Core\Attribute\AttributeKeyInterface|string $handle
     * @param mixed $first
     * @param mixed $second
     * @param null|mixed $firstStatic
     * @param null|mixed $secondStatic
     */
    public function testSetAttribute($handle, $first, $second, $firstStatic = null, $secondStatic = null)
    {
        $this->getAttributeObjectForSet()->setAttribute($handle, $first);
        $attribute = $this->getAttributeObjectForGet()->getAttribute($handle);
        if ($firstStatic != null) {
            $this->assertSame($firstStatic, $attribute);
        } else {
            $this->assertSame($first, $attribute);
        }
    }

    /**
     *  @dataProvider attributeValues
     *
     * @param \Concrete\Core\Attribute\AttributeKeyInterface|string $handle
     * @param mixed $first
     * @param mixed $second
     * @param null|mixed $firstStatic
     * @param null|mixed $secondStatic
     */
    public function testResetAttributes($handle, $first, $second, $firstStatic = null, $secondStatic = null)
    {
        $object = $this->getAttributeObjectForSet();
        $object->setAttribute($handle, $second);
        $object = $this->getAttributeObjectForGet();
        $object->reindex();
        if (method_exists($object, 'refreshCache')) {
            $object->refreshCache();
        }
        $attribute = $this->getAttributeObjectForGet()->getAttribute($handle);
        if ($secondStatic != null) {
            $this->assertSame($attribute, $secondStatic);
        } else {
            $this->assertSame($attribute, $second);
        }
    }

    /**
     *  @dataProvider attributeIndexTableValues
     *
     * @param \Concrete\Core\Attribute\AttributeKeyInterface|string $handle
     * @param mixed $value
     * @param mixed $columns
     */
    public function testReindexing($handle, $value, $columns)
    {
        $object = $this->getAttributeObjectForSet();
        $object->setAttribute($handle, $value);
        $object = $this->getAttributeObjectForGet();
        $object->reindex();

        $db = Database::get();
        $r = $db->query($this->indexQuery);
        $row = $r->fetch();
        foreach ($columns as $column => $value) {
            $this->assertTrue(isset($row[$column]));
            $this->assertEquals($value, $row[$column]);
        }
    }

    /**
     *  @dataProvider attributeHandles
     *
     * @param string $handle
     */
    public function testUnsetAttributes($handle)
    {
        $object = $this->getAttributeObjectForSet();
        $ak = call_user_func_array([$this->getAttributeKeyClass(), 'getByHandle'], [$handle]);
        $object->clearAttribute($ak);
        $object = $this->getAttributeObjectForGet();
        $cav = $object->getAttributeValueObject($ak);
        if (is_object($cav)) {
            $this->fail(t("clearAttribute did not delete '%s'.", $handle));
        }
    }

    abstract protected function getAttributeKeyClass();

    abstract protected function installAttributeCategoryAndObject();

    protected function getAttributeObjectForSet()
    {
        return $this->object;
    }

    protected function getAttributeObjectForGet()
    {
        return $this->object;
    }
}
