<?php


require_once __DIR__ . "/ObjectBuilderTestTrait.php";

class ObjectBuilderTest extends PHPUnit_Framework_TestCase
{
    use \ObjectBuilderTestTrait;

    public function testCreateDataObject()
    {
        $builder = $this->getObjectBuilder();

        /*
         * @var \Concrete\Core\Entity\Express\Entity
         */
        $object = $builder->buildObject();
        $this->assertInstanceOf('\Concrete\Core\Entity\Express\Entity', $object);
        $this->assertEquals(4, count($object->getAttributes()));
        $attributes = $object->getAttributes();

        $this->assertInstanceOf('\Concrete\Core\Entity\Attribute\Key\Key', $attributes[0]);
        $this->assertInstanceOf('\Concrete\Core\Entity\Attribute\Key\Key', $attributes[1]);
        $this->assertInstanceOf('\Concrete\Core\Entity\Attribute\Key\Key', $attributes[2]);

        $this->assertInstanceOf('\Concrete\Core\Entity\Attribute\Key\Type\TextType', $attributes[0]->getAttributeKeyType());
        $this->assertInstanceOf('\Concrete\Core\Entity\Attribute\Key\Type\TextType', $attributes[1]->getAttributeKeyType());
        $this->assertInstanceOf('\Concrete\Core\Entity\Attribute\Key\Type\TextareaType', $attributes[2]->getAttributeKeyType());

        /** @var $first \Concrete\Core\Entity\Attribute\Key\Key */
        $first = $attributes[0];
        $this->assertEquals('First Name', $first->getAttributeKeyName());
        $this->assertEquals(false, $first->isAttributeKeyContentIndexed());
        $this->assertEquals(true, $first->isAttributeKeySearchable());

        /** @var $bio \Concrete\Core\Entity\Attribute\Key\Key */
        $bio = $attributes[2];
        $this->assertEquals(true, $bio->isAttributeKeyContentIndexed());
        $this->assertEquals(true, $bio->isAttributeKeySearchable());

        $key_type = $bio->getAttributeKeyType();
        $this->assertEquals('text', $key_type->getMode());

    }
}
