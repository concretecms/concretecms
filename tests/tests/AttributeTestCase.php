<?php
use \Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Key\Category;

abstract class AttributeTestCase extends ConcreteDatabaseTestCase {
	
	protected $fixtures = array();
    protected $tables = array(
        'AttributeKeys',
        'AttributeKeyCategories',
        'AttributeTypes',
        'AttributeValues',
        'Packages',
        'atBooleanSettings',
        'atBoolean',
        'atDefault',
        'atTextarea',
        'atTextareaSettings'
    );
    protected $object;
    protected $keys = array();
    protected $keyObjects = array();
    abstract protected function getAttributeKeyClass();
    abstract public function attributeValues();
    abstract public function attributeHandles();

    protected function getAttributeObjectForSet()
    {
        return $this->object;
    }

    protected function getAttributeObjectForGet()
    {
        return $this->object;
    }


    protected function setUp() {
        parent::setUp();
        AttributeType::add('boolean', 'Boolean');
        AttributeType::add('textarea', 'Textarea');
        AttributeType::add('text', 'text');
        foreach($this->keys as $akHandle => $args) {
            $args['akHandle'] = $akHandle;
            $type = AttributeType::getByHandle($args['type']);
            $this->keys[] = call_user_func_array(array($this->getAttributeKeyClass(), 'add'), array($type, $args));
        }

    }

    /**
     *  @dataProvider attributeValues
     */
    public function testSetAttribute($handle,$first,$second,$firstStatic=null,$secondStatic=null) {
        $this->getAttributeObjectForSet()->setAttribute($handle,$first);
        $attribute = $this->getAttributeObjectForGet()->getAttribute($handle);
        if($firstStatic != null){
            $this->assertSame($attribute,$firstStatic);
        } else {
            $this->assertSame($attribute,$first);
        }
    }

    /**
     *  @dataProvider attributeValues
     */
    public function testResetAttributes($handle,$first,$second,$firstStatic=null,$secondStatic=null) {
        $object = $this->getAttributeObjectForSet();
        $object->setAttribute($handle,$second);
        $object = $this->getAttributeObjectForGet();
        $object->reindex();
        $object->refreshCache();
        $attribute = $this->getAttributeObjectForGet()->getAttribute($handle);
        if($secondStatic != null){
            $this->assertSame($attribute,$secondStatic);
        } else {
            $this->assertSame($attribute,$second);
        }
    }

    /**
     *  @dataProvider attributeHandles
     */
    public function testUnsetAttributes($handle) {
        $object = $this->getAttributeObjectForSet();
        $ak = call_user_func_array(array($this->getAttributeKeyClass(), 'getByHandle'), array($handle));
        $object->clearAttribute($ak);
        $object = $this->getAttributeObjectForGet();
        $cav = $object->getAttributeValueObject($ak);
        if(is_object($cav)) {
            $this->fail(t("clearAttribute did not delete '%s'.",$handle));
        }
    }




}