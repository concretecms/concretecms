<?php


require_once __DIR__ . "/ObjectBuilderTestTrait.php";
require_once __DIR__ . "/ExpressEntityManagerTestCaseTrait.php";

class ManyToManyTest extends PHPUnit_Framework_TestCase
{
    use \ExpressEntityManagerTestCaseTrait;

    protected function getEntityManager()
    {
        $user = new \Concrete\Core\Entity\Express\Entity();
        $user->setTableName('Users');
        $user->setName('User');

        $group = new \Concrete\Core\Entity\Express\Entity();
        $group->setTableName('Groups');
        $group->setName('Group');

        $builder = Core::make('express.builder.association');
        $builder->addManyToMany($user, $group, 'users', 'groups');

        return $this->deliverEntityManager(array($user, $group));
    }
    public function tearDown()
    {
        parent::tearDown();
        if (file_exists(__DIR__ . '/Testing/User.php')) {
            unlink(__DIR__ . '/Testing/User.php');
        }
        if (file_exists(__DIR__ . '/Testing/Group.php')) {
            unlink(__DIR__ . '/Testing/Group.php');
        }
        if (is_dir(__DIR__ . '/Testing')) {
            rmdir(__DIR__ . '/Testing');
        }
    }

    public function testOutputClassWritingAndNamespace()
    {
        $writer = Core::make('express.writer');
        $writer->setNamespace('Testing');
        $writer->setEntityManager($this->getEntityManager());
        $writer->setOutputPath(__DIR__);

        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('User');
        $entity->setTableName('Users');
        $writer->writeClass($entity);

        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Group');
        $entity->setTableName('Groups');
        $writer->writeClass($entity);

        $this->assertFileExists(__DIR__ . '/Testing/User.php');
        require_once __DIR__ . '/Testing/User.php';

        $this->assertTrue(class_exists('\Testing\User', false));
        $class = new ReflectionClass('\Testing\User');
        $this->assertTrue($class->isSubclassOf('\Concrete\Core\Express\BaseEntity'));
        $this->assertTrue($class->hasMethod('getId'));
        $this->assertTrue($class->hasProperty('groups'));
        $this->assertTrue($class->hasMethod('getGroups'));

        $this->assertFileExists(__DIR__ . '/Testing/Group.php');
        require_once __DIR__ . '/Testing/Group.php';

        $this->assertTrue(class_exists('\Testing\Group', false));
        $class = new ReflectionClass('\Testing\Group');
        $this->assertTrue($class->isSubclassOf('\Concrete\Core\Express\BaseEntity'));
        $this->assertTrue($class->hasMethod('getId'));
        $this->assertTrue($class->hasProperty('users'));
        $this->assertTrue($class->hasMethod('getUsers'));
    }

    public function testCreateDatabase()
    {
        $em = $this->getEntityManager();
        $factory = new \Concrete\Core\Express\BackendEntityManagerFactory(
            Core::make('app'),
            $em
        );
        $manager = new \Concrete\Core\Express\SchemaManager($factory);
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('User');
        $create = $manager->getCreateSql($entity);

        $this->assertTrue(is_array($create));
        $this->assertEquals(3, count($create));
        $this->assertContains('create table', $create[0], '', true);
        $this->assertContains('ExpressUsers', $create[0]);
        $this->assertContains('id', $create[0]);
        $this->assertContains('auto_increment', $create[0], '', true);

        $this->assertContains('create table', $create[1], '', true);
        $this->assertContains('ExpressUsersGroups', $create[1]);
        $this->assertContains('usersID', $create[1]);

        $this->assertContains('alter table', $create[2], '', true);
        $this->assertContains('ExpressUsersGroups', $create[2]);

        $manager = new \Concrete\Core\Express\SchemaManager($factory);
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Group');
        $create = $manager->getCreateSql($entity);

        $this->assertEquals(1, count($create));

        $this->assertContains('create table', $create[0], '', true);
        $this->assertContains('ExpressGroups', $create[0]);
        $this->assertContains('id', $create[0]);
    }
}
