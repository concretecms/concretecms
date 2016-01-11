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
        $this->assertInstanceOf('\Concrete\Core\Entity\AttributeKey\TextAttributeKey', $attributes[0]);
        $this->assertInstanceOf('\Concrete\Core\Entity\AttributeKey\TextAttributeKey', $attributes[1]);
        $this->assertInstanceOf('\Concrete\Core\Entity\AttributeKey\TextAreaAttributeKey', $attributes[2]);

        /** @var $first \Concrete\Core\Entity\AttributeKey\TextAttributeKey */
        $first = $attributes[0];
        $this->assertEquals('First Name', $first->getAttributeKeyName());
        $this->assertEquals(false, $first->isAttributeKeyContentIndexed());
        $this->assertEquals(true, $first->isAttributeKeySearchable());

        /** @var $bio \Concrete\Core\Entity\AttributeKey\TextAreaAttributeKey */
        $bio = $attributes[2];
        $this->assertEquals('rich_text', $bio->getMode());
        $this->assertEquals(true, $bio->isAttributeKeyContentIndexed());
        $this->assertEquals(true, $bio->isAttributeKeySearchable());
    }
}
