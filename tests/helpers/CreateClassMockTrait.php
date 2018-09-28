<?php

namespace Concrete\TestHelpers;

use RuntimeException;

trait CreateClassMockTrait
{
    /**
     * The method we use to create mocks.
     *
     * @var string
     */
    private static $mockCreateMethod;

    /**
     * Returns a mock object for the specified class.
     *
     * @param string $className
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockFromClass($className)
    {
        if (!isset(self::$mockCreateMethod)) {
            $methods = ['createMock', 'createTestDouble', 'getMock'];

            foreach ($methods as $method) {
                if (method_exists($this, $method)) {
                    static::$mockCreateMethod = $method;
                    break;
                }
            }
        }

        if (isset(self::$mockCreateMethod)) {
            return $this->{self::$mockCreateMethod}($className);
        }

        throw new RuntimeException('Unable to figure out how to create mock objects.');
    }
}
