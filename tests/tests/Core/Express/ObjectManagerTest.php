<?php

require_once __DIR__ . '/ExpressEntityManagerTestCaseTrait.php';

class ObjectManagerTest extends PHPUnit_Framework_TestCase
{

    use ExpressEntityManagerTestCaseTrait;

    public function tearDown()
    {
        parent::tearDown();
        if (file_exists(__DIR__ . '/Concrete/Express/Student.php')) {
            unlink(__DIR__ . '/Concrete/Express/Student.php');
        }
        if (is_dir(__DIR__ . '/Concrete/Express')) {
            rmdir(__DIR__ . '/Concrete/Express');
        }
        if (is_dir(__DIR__ . '/Concrete')) {
            rmdir(__DIR__ . '/Concrete');
        }
    }

    public function setUp()
    {
        parent::setUp();
        $writer = Core::make('express.writer');
        $writer->setEntityManager($this->getMockEntityManager());
        $writer->setOutputPath(__DIR__);

        $strictLoader = new \Symfony\Component\ClassLoader\Psr4ClassLoader();
        $strictLoader->addPrefix(Config::get('express.entity_classes.namespace'), __DIR__ . '/Concrete/Express');
        $strictLoader->register();

        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Student');
        $entity->setTableName('Students');
        $writer->writeClass($entity);
    }

    public function testBackendCreateAPI()
    {
        $em = $this->getMockEntityManager();
        Core::bind('Doctrine\ORM\EntityManager', function() use ($em) {
            return $em;
        });

        $express = Core::make('express');
        $student = $express->create('Student');
        $express->set($student, 'first_name', 'Andrew');
        $express->save($student);

        $this->assertInstanceOf('\Concrete\Express\Student', $student);
        $this->assertEquals($student->getFirstName(), 'Andrew');
        $this->assertEquals($student->getProperty('first_name'), 'Andrew');
    }

}
