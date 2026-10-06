<?php

declare(strict_types=1);

namespace Concrete\Tests;

use Concrete\Core\Area\CustomStyleRepository as AreaCustomStyleRepository;
use Concrete\Core\Block\CustomStyleRepository as BlockCustomStyleRepository;
use Concrete\Core\Permission\Category as PermissionCategory;
use Concrete\Core\User\User;
use Mockery\Adapter\Phpunit\MockeryTestCase as PHPUnitTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class TestCase extends PHPUnitTestCase
{
    public static function tearDownAfterClass(): void
    {
        static::resetApplicationState();
        parent::tearDownAfterClass();
    }

    /**
     * Forget the state that a test may leave in the application and that would affect the next tests.
     */
    protected static function resetApplicationState(): void
    {
        $app = app();
        // Clear the session, if it has been started
        if ($app->resolved('session')) {
            $app->make('session')->clear();
        }
        // The currently logged in user may belong to groups that no longer exist
        $app->forgetInstance(User::class);
        // The custom styles are kept in memory by collection ID, and the next test case reuses those IDs
        $app->forgetInstance(AreaCustomStyleRepository::class);
        $app->forgetInstance(BlockCustomStyleRepository::class);
        // The permission categories are kept in a static property of their class
        \Closure::bind(static function (): void {
            PermissionCategory::$categories = null;
        }, null, PermissionCategory::class)();
    }

    protected static function setNonPublicPropertyValues(object $object, array $properties): void
    {
        foreach ($properties as $propertyName => $propertyValue) {
            self::setNonPublicPropertyValue($object, $propertyName, $propertyValue);
        }
    }

    protected static function setNonPublicPropertyValue(object $object, string $propertyName, $propertyValue): void
    {
        $property = new \ReflectionProperty($object, $propertyName);
        if (PHP_VERSION_ID < 80100) { // As of PHP 8.1.0, calling this method has no effect
            $property->setAccessible(true);
        }
        $property->setValue($object, $propertyValue);
    }
}
