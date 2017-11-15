<?php

namespace Concrete\Tests\Utility\Service\Validation;

use PHPUnit_Framework_TestCase;

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
            [false, 0, 1],
            [true, 123, 0],
            [true, 123, 123],
            [true, '123', 123],
            [false, 123, 124],
            [true, '123', null, 123],
            [false, '123', null, 122],
        ];
    }

    /**
     * @dataProvider integerDataProvider
     *
     * @param mixed $expected
     * @param mixed $input1
     * @param null|mixed $min
     * @param null|mixed $max
     */
    public function testInteger($expected, $input1, $min = null, $max = null)
    {
        $this->assertEquals($expected, $this->object->integer($input1, $min, $max));
    }

    public function numberDataProvider()
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
            [true, '1.25'],
            [true, 1.25],
            [false, 0, 1],
            [true, 123, 0],
            [true, 123, 123],
            [true, '123', 123],
            [false, 123, 124],
            [true, '123', null, 123],
            [false, '123', null, 122],
            [false, '.'],
            [false, '-'],
            [false, '-.'],
            [true, '.0'],
            [true, '0', 0, 1],
            [true, '.1', 0, 1],
            [true, '.999', 0, 1],
            [true, .05, 0, 0.1],
        ];
    }

    /**
     * @dataProvider numberDataProvider
     *
     * @param mixed $expected
     * @param mixed $input1
     * @param null|mixed $min
     * @param null|mixed $max
     */
    public function testNumber($expected, $input1, $min = null, $max = null)
    {
        $this->assertEquals($expected, $this->object->number($input1, $min, $max));
    }
}
