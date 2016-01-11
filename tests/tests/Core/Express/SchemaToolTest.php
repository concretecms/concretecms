<?php


require_once "ExpressEntityManagerTestCaseTrait.php";

class SchemaToolTest extends PHPUnit_Framework_TestCase
{
    use ExpressEntityManagerTestCaseTrait;

    public function testSchemaCreate()
    {
        $factory = new \Concrete\Core\Express\BackendEntityManagerFactory(
            Core::make('app'),
            $this->getMockEntityManager()
        );
        $manager = new \Concrete\Core\Express\SchemaManager($factory);
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Student');
        $create = $manager->getCreateSql($entity);

        $this->assertTrue(is_array($create));
        $this->assertEquals(1, count($create));
        $this->assertContains('create table', $create[0], '', true);
        $this->assertContains('ExpressStudents', $create[0]);
        $this->assertContains('id', $create[0]);
        $this->assertContains('first_name', $create[0]);
        $this->assertContains('auto_increment', $create[0], '', true);
    }
}
