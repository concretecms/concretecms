<?php
use Concrete\Core\Express\ObjectBuilder;

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
        $em = $factory->create(\Database::connection());
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Student');
        $metadata = $em->getClassMetadata($factory->getClassName($entity));
        $create = $tool->getCreateSchemaSql(array($metadata));

        $this->assertTrue(is_array($create));
        $this->assertEquals(1, count($create));
        $this->assertContains('create table', $create[0], '', true);
        $this->assertContains('ExpressStudents', $create[0]);
        $this->assertContains('id', $create[0]);
        $this->assertContains('first_name', $create[0]);
        $this->assertContains('auto_increment', $create[0], '', true);

    }

}
