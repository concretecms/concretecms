<?php

class FormControlTypeTest extends PHPUnit_Framework_TestCase
{
    public function testList()
    {
        $manager = \Core::make('express.control.type.manager');
        $this->assertInstanceOf('\Concrete\Core\Express\Form\Control\Type\Manager', $manager)
;
        $drivers = $manager->getDrivers();
        $this->assertCount(3, $drivers);
        $this->assertInstanceOf('\Concrete\Core\Express\Form\Control\Type\EntityPropertyType', $drivers['entity_property']);
        $this->assertInstanceOf('\Concrete\Core\Express\Form\Control\Type\AttributeKeyType', $drivers['attribute_key']);
        $this->assertInstanceOf('\Concrete\Core\Express\Form\Control\Type\AssociationType', $drivers['association']);
        $expected = array('entity_property', 'attribute_key', 'association');
        $i = 0;
        foreach ($drivers as $key => $driver) {
            $this->assertEquals($expected[$i], $key);
            ++$i;
        }
    }
}
