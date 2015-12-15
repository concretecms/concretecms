<?php

use Concrete\Core\Express\DoctrineMappingDriver;
require_once __DIR__ . '/ExpressEntityManagerTestCaseTrait.php';

class DoctrineMappingDriverTest extends PHPUnit_Framework_TestCase
{

    use \ExpressEntityManagerTestCaseTrait;

    public function testGetAllClassNames()
    {
        $driver = new DoctrineMappingDriver(Core::make('app'), $this->getMockEntityManager());
        $classNames = $driver->getAllClassNames();
        $this->assertEquals(2, count($classNames));
        $this->assertEquals('Student', $classNames[0]);
        $this->assertEquals('Teacher', $classNames[1]);
    }

    public function testNamespace()
    {
        $driver = new DoctrineMappingDriver(Core::make('app'), $this->getMockEntityManager());
        $driver->setNamespace('Express');
        $classNames = $driver->getAllClassNames();
        $this->assertEquals(2, count($classNames));
        $this->assertEquals('Express\Student', $classNames[0]);
        $this->assertEquals('Express\Teacher', $classNames[1]);
    }

    public function testIsTransient()
    {
        $driver = new DoctrineMappingDriver(Core::make('app'), $this->getMockEntityManager());
        $this->assertTrue($driver->isTransient('Student'));
        $this->assertFalse($driver->isTransient('Concrete\Core\Captcha\Library'));
        $this->assertTrue($driver->isTransient('Teacher'));
    }

    public function testLoadMetadataForClass()
    {
        $driver = new DoctrineMappingDriver(Core::make('app'), $this->getMockEntityManager());
        $metadata1 = new \Doctrine\ORM\Mapping\ClassMetadata('\Student');
        $metadata2 = new \Doctrine\ORM\Mapping\ClassMetadata('\Teacher');
        $driver->loadMetadataForClass('Student', $metadata1);
        $driver->loadMetadataForClass('Teacher', $metadata2);

        $this->assertEquals('ExpressStudents', $metadata1->getTableName());
        $this->assertEquals('ExpressTeachers', $metadata2->getTableName());

        $names = $metadata1->getColumnNames();
        $this->assertContains('id', $names);
    }

}
