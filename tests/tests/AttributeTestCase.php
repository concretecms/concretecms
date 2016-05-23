<?php

use \Concrete\Core\Attribute\Type as AttributeType;

abstract class AttributeTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $metadatas = array(
        'Concrete\Core\Entity\Attribute\Category',
        'Concrete\Core\Entity\Attribute\Key\Type\TextType',
        'Concrete\Core\Entity\Attribute\Key\Type\TextareaType',
        'Concrete\Core\Entity\Attribute\Key\Type\BooleanType',
        'Concrete\Core\Entity\Attribute\Key\Type\Type',
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
    );
    protected $object;
    protected $keys = array();
    protected $keyObjects = array();
    abstract protected function getAttributeKeyClass();
    abstract public function attributeValues();
    abstract public function attributeHandles();
    abstract protected function installAttributeCategoryAndObject();

    protected function getAttributeObjectForSet()
    {
        return $this->object;
    }

    protected function getAttributeObjectForGet()
    {
        return $this->object;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->installAttributeCategoryAndObject();
        AttributeType::add('boolean', 'Boolean');
        AttributeType::add('textarea', 'Textarea');
        AttributeType::add('number', 'number');
        AttributeType::add('text', 'text');
        foreach ($this->keys as $akHandle => $args) {
            $args['akHandle'] = $akHandle;
            $type = AttributeType::getByHandle($args['type']);
            $this->keys[] = call_user_func_array(array($this->getAttributeKeyClass(), 'add'), array($type, $args));
        }
    }

    /**
     *  @dataProvider attributeValues
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
     */
    public function testUnsetAttributes($handle)
    {
        $object = $this->getAttributeObjectForSet();
        $ak = call_user_func_array(array($this->getAttributeKeyClass(), 'getByHandle'), array($handle));
        $object->clearAttribute($ak);
        $object = $this->getAttributeObjectForGet();
        $cav = $object->getAttributeValueObject($ak);
        if (is_object($cav)) {
            $this->fail(t("clearAttribute did not delete '%s'.", $handle));
        }
    }
}
