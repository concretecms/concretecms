<?php

class NumbersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Utility\Service\Validation\Numbers
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Concrete\Core\Utility\Service\Validation\Numbers();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
        parent::tearDown();
    }

    public function integerDataProvider()
    {
        return array(
            array(true, '0'),
            array(true, 0),
            array(true, '1'),
            array(true, 1),
            array(true, '-1456789445'),
            array(true, -1456789445),
            array(true, '123'),
            array(true, 123),
            array(false, '123,456'),
            array(false, 'a'),
            array(false, false),
            array(false, true),
            array(false, null),
            array(false, ''),
            array(false, '1.25'),
            array(false, 1.25),
        );
    }

    /**
     * @dataProvider integerDataProvider
     */
    public function testInteger($expected, $input1)
    {
        $this->assertEquals($expected, $this->object->integer($input1));
    }
}
