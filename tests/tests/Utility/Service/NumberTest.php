<?php

namespace Concrete\Tests\Utility\Service;

use Concrete\Core\Utility\Service\Number;
use PHPUnit_Framework_Testcase;

class NumberTest extends PHPUnit_Framework_Testcase
{
    public function flexRoundDataProvider()
    {
        return [
            ['00010.0000', 10],
            ['012.12', 12.12],
            ['1234.5432', 1234.5432],
            ['1204.50001', 1204.50001],
            ['1205.00000', 1205],
            ['1206', 1206],
        ];
    }

    /**
     * @dataProvider flexRoundDataProvider
     *
     * @param mixed $test
     * @param mixed $value
     */
    public function testFlexRound($test, $value)
    {
        $numberService = new Number();
        $this->assertEquals($value, $numberService->flexround($test));
    }

    public function trimDataProvider()
    {
        return [
            ['00010.0000', '10'],
            ['012.12', '12.12'],
            ['1234.5432', '1234.5432'],
            ['1204.50001', '1204.50001'],
            ['1205.00000', '1205'],
            ['1206', '1206'],
            [null, ''],
            [false, ''],
            ['', ''],
            ['.', '0'],
            ['000000000.000000', '0'],
            ['+000000000.000000', '+0'],
            ['-000000000.000000', '-0'],
            ['12.34', '12.34'],
            ['+12.34', '+12.34'],
            ['-12.34', '-12.34'],
            ['00012000.00034000', '12000.00034'],
            ['+00012000.00034000', '+12000.00034'],
            ['-00012000.00034000', '-12000.00034'],
            ['00000000123456789012345678901234567890000.000000000012345678901234567890123456789000000000', '123456789012345678901234567890000.000000000012345678901234567890123456789'],
        ];
    }

    /**
     * @dataProvider trimDataProvider
     *
     * @param mixed $test
     * @param mixed $expected
     */
    public function testTrim($test, $expected)
    {
        $numberService = new Number();
        $this->assertSame($expected, $numberService->trim($test));
    }
}
