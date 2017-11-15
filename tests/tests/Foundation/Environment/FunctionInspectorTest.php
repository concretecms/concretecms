<?php

namespace Concrete\Tests\Foundation\Environment;

use Concrete\Core\Foundation\Environment\FunctionInspector;
use Concrete\Core\Support\Facade\Application;
use PHPUnit_Framework_TestCase;

class FunctionInspectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FunctionInspector
     */
    private static $functionInspector;

    public static function setupBeforeClass()
    {
        self::$functionInspector = Application::getFacadeApplication()->make(FunctionInspector::class);
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
     *
     * @param mixed $disabledFunctions
     * @param mixed $functionName
     * @param mixed $expected
     */
    public function testFunctionAvailable($disabledFunctions, $functionName, $expected)
    {
        self::$functionInspector->setDisabledFunctions($disabledFunctions);
        $this->assertSame($expected, self::$functionInspector->functionAvailable($functionName));
    }
}
