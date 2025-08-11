<?php

namespace Concrete\Tests;

use Mockery\Adapter\Phpunit\MockeryTestCase as PHPUnitTestCase;
use ReflectionProperty;

class TestCase extends PHPUnitTestCase
{
    protected static function setNonPublicPropertyValues(object $object, array $properties): void
    {
        foreach ($properties as $propertyName => $propertyValue) {
            self::setNonPublicPropertyValue($object, $propertyName, $propertyValue);
        }
    }

    protected static function setNonPublicPropertyValue(object $object, string $propertyName, $propertyValue): void
    {
        $property = new ReflectionProperty($object, $propertyName);
        if (PHP_VERSION_ID < 80100) { // As of PHP 8.1.0, calling this method has no effect
            $property->setAccessible(true);
        }
        $property->setValue($object, $propertyValue);
    }
}
