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
        return [
            [true, '0'],
            [true, 0],
            [true, '1'],
            [true, 1],
            [true, '-1456789445'],
            [true, -1456789445],
            [true, '123'],
            [true, 123],
            [false, '123,456'],
            [false, 'a'],
            [false, false],
            [false, true],
            [false, null],
            [false, ''],
            [false, '1.25'],
            [false, 1.25],
        ];
    }

    /**
     * @dataProvider integerDataProvider
     */
    public function testInteger($expected, $input1)
    {
        $this->assertEquals($expected, $this->object->integer($input1));
    }
}
