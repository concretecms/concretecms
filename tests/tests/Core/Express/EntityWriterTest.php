<?php
use Concrete\Core\Express\ObjectBuilder;

require_once "ExpressEntityManagerTestCaseTrait.php";

class EntityWriterTest extends PHPUnit_Framework_TestCase
{

    use ExpressEntityManagerTestCaseTrait;

    public function tearDown()
    {
        parent::tearDown();
        return;
        if (file_exists(__DIR__ . '/Testing/Student.php')) {
            unlink(__DIR__ . '/Testing/Student.php');
        }
        if (is_dir(__DIR__ . '/Testing')) {
            rmdir(__DIR__ . '/Testing');
        }
    }

    public function testOutputClassNoNamespaceDefined()
    {
        $this->setExpectedException('\Concrete\Core\Express\Exception\NoNamespaceDefinedException');
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $writer = new \Concrete\Core\Express\EntityWriter($this->getMockEntityManager(), Core::make('app'));
        $writer->createClass($entity);
    }
    public function testOutputClassNoLocationDefined()
    {
        $this->setExpectedException('\Concrete\Core\Express\Exception\InvalidClassLocationDefinedException');
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $writer = new \Concrete\Core\Express\EntityWriter($this->getMockEntityManager(), Core::make('app'));
        $writer->setNamespace('Express');
        $writer->createClass($entity);
    }

    public function testOutputClassStandardConfiguration()
    {
        $writer = Core::make('express.writer');
        $this->assertEquals(Config::get('express.entity_classes.output_path'), $writer->getOutputPath());
        $this->assertEquals(Config::get('express.entity_classes.namespace'), $writer->getNamespace());
    }

    public function testOutputClassWritingAndNamespace()
    {

        $writer = Core::make('express.writer');
        $writer->setNamespace('Testing');
        $writer->setEntityManager($this->getMockEntityManager());
        $writer->setOutputPath(__DIR__);

        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Student');
        $entity->setTableName('Students');
        $writer->createClass($entity);

        $this->assertFileExists(__DIR__ . '/Testing/Student.php');
        require_once __DIR__ . '/Testing/Student.php';

        $this->assertTrue(class_exists('\Testing\Student', false));
        $class = new ReflectionClass('\Testing\Student');
        $this->assertTrue($class->hasMethod('getId'));
        $this->assertTrue($class->hasMethod('setId'));
        $this->assertTrue($class->hasMethod('getFirstName'));
        $this->assertTrue($class->hasMethod('setFirstName'));
    }

}
