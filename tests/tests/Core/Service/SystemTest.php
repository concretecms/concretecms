<?php
namespace Concrete\Tests\Core\Service;

use Concrete\Core\Service\System;
use Concrete\Core\Support\Facade\Application;
use PHPUnit_Framework_TestCase;

class SystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var System
     */
    private static $system;

    public static function setupBeforeClass()
    {
        self::$system = Application::getFacadeApplication()->make(System::class);
    }

    public function functionAvailableProvider()
    {
        return [
            [[], 'not-existing-function', false],
            [[], 'function_exists', true],
            [null, 'function_exists', true],
            [null, 'Function_Exists', true],
            [['another_function1', 'another_function2'], 'Function_Exists', true],
            [['another_function1', 'FuNcTiOn_ExIsTs ', 'another_function2'], 'Function_Exists', false],
        ];
    }

    /**
     *  @dataProvider functionAvailableProvider
     */
    public function testFunctionAvailable($disabledFunctions, $functionName, $expected)
    {
        self::$system->setDisabledFunctions($disabledFunctions);
        $this->assertSame($expected, self::$system->functionAvailable($functionName));
    }
}
