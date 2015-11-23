<?php
use Concrete\Core\Express\ObjectBuilder;

class ObjectBuilderTest extends PHPUnit_Framework_TestCase
{

    protected function getBuilder()
    {
        /**
         * @var $builder \Concrete\Core\Express\ObjectBuilder;
         */
        $builder = Core::make('express.builder');
        $builder->createObject('Student')
            ->addAttribute('text', 'First Name')
            ->addAttribute('text', 'Last Name');
        $builder->createAttribute('text_area', 'Bio')
            ->setMode('rich_text')
            ->setIsIndexed(true)
            ->build();
        $builder->addAttribute('text', 'Password');
        return $builder;

    }
    public function testCreateDataObject()
    {
        $builder = $this->getBuilder();

        /**
         * @var $object \Concrete\Core\Entity\Express\Entity
         */
        $object = $builder->getObject();
        $this->assertInstanceOf('\Concrete\Core\Entity\Express\Entity', $object);
        $this->assertEquals(4, count($object->getAttributes()));
        $attributes = $object->getAttributes();
        $this->assertInstanceOf('\Concrete\Core\Entity\AttributeKey\TextAttributeKey', $attributes[0]);
        $this->assertInstanceOf('\Concrete\Core\Entity\AttributeKey\TextAttributeKey', $attributes[1]);
        $this->assertInstanceOf('\Concrete\Core\Entity\AttributeKey\TextAreaAttributeKey', $attributes[2]);

        /** @var $first \Concrete\Core\Entity\AttributeKey\TextAttributeKey */
        $first = $attributes[0];
        $this->assertEquals('First Name', $first->getName());
        $this->assertEquals(false, $first->getIsIndexed());
        $this->assertEquals(true, $first->getIsSearchable());

        /** @var $bio \Concrete\Core\Entity\AttributeKey\TextAreaAttributeKey */
        $bio = $attributes[2];
        $this->assertEquals('rich_text', $bio->getMode());
        $this->assertEquals(true, $bio->getIsIndexed());
        $this->assertEquals(true, $bio->getIsSearchable());
    }


}
