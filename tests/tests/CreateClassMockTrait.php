<?php
namespace Concrete\Tests;

trait CreateClassMockTrait
{
    /**
     * Flag to remember how we should create mocks.
     *
     * @var int|null
     */
    private static $mockCreatorVersion;

    /**
     * Returns a mock object for the specified class.
     *
     * @param string $className
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockFromClass($className)
    {
        if (!isset(self::$mockCreatorVersion)) {
            $v = \PHPUnit_Runner_Version::id();
            if (version_compare($v, '5.4') < 0) {
                self::$mockCreatorVersion = 1;
            } else {
                self::$mockCreatorVersion = 2;
            }
        }
        switch (self::$mockCreatorVersion) {
            case 1:
                return $this->getMock($className);
            case 2:
                if (method_exists([$this, 'createMock'])) {
                    return $this->createMock($className);
                }
                if (method_exists($this, 'createTestDouble')) {
                    return $this->createTestDouble($className);
                }
                throw new \RuntimeException('Invalid PHPUnit version, no createMock method found.');
        }
    }
}
