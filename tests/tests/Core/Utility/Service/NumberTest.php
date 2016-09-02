<?php

namespace Concrete\tests\Core\Utility\Service;

use Concrete\Core\Utility\Service\Number;

class NumberTest extends \PHPUnit_Framework_Testcase
{

    public function flexRoundDataProvider()
    {
        return [
            ["00010.0000", 10],
            ["012.12", 12.12],
            ["1234.5432", 1234.5432],
            ["1204.50001", 1204.50001],
            ["1205.00000", 1205],
            ["1206", 1206],
        ];
    }

    /**
     * @dataProvider flexRoundDataProvider
     */
    public function testFlexRound($test, $value)
    {
        $numberService = new Number();
        $this->assertEquals($value, $numberService->flexround($test));
    }

}
