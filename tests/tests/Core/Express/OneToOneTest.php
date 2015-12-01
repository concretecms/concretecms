<?php
use Concrete\Core\Express\ObjectBuilder;

require_once __DIR__ . "/ObjectBuilderTestTrait.php";
require_once __DIR__ . "/ExpressEntityManagerTestCaseTrait.php";

class OneToOneTest extends PHPUnit_Framework_TestCase
{

    use \ExpressEntityManagerTestCaseTrait;

    protected function getEntityManager()
    {
        $user = new \Concrete\Core\Entity\Express\Entity();
        $user->setTableName('Users');
        $user->setName('User');

        $collection = new \Concrete\Core\Entity\Express\Entity();
        $collection->setTableName('UserFavoriteCollections');
        $collection->setName('UserFavoriteCollection');

        $builder = Core::make('express.builder.association');
        $builder->addOneToOne($user, $collection);

        return $this->deliverEntityManager(array($user, $collection));

    }
    public function tearDown()
    {
        parent::tearDown();
        if (file_exists(__DIR__ . '/Testing/User.php')) {
            unlink(__DIR__ . '/Testing/User.php');
        }
        if (file_exists(__DIR__ . '/Testing/UserFavoriteCollection.php')) {
            unlink(__DIR__ . '/Testing/UserFavoriteCollection.php');
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
        $writer->createClass($entity);

        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('UserFavoriteCollection');
        $entity->setTableName('UserFavoriteCollections');
        $writer->createClass($entity);

        $this->assertFileExists(__DIR__ . '/Testing/User.php');
        require_once __DIR__ . '/Testing/User.php';

        $this->assertTrue(class_exists('\Testing\User', false));
        $class = new ReflectionClass('\Testing\User');
        $this->assertTrue($class->isSubclassOf('\Concrete\Core\Express\BaseEntity'));
        $this->assertTrue($class->hasMethod('getId'));
        $this->assertTrue($class->hasProperty('user_favorite_collection'));
        $this->assertTrue($class->hasMethod('getUserFavoriteCollection'));

        $this->assertFileExists(__DIR__ . '/Testing/UserFavoriteCollection.php');
        require_once __DIR__ . '/Testing/UserFavoriteCollection.php';

        $this->assertTrue(class_exists('\Testing\UserFavoriteCollection', false));
        $class = new ReflectionClass('\Testing\UserFavoriteCollection');
        $this->assertTrue($class->isSubclassOf('\Concrete\Core\Express\BaseEntity'));
        $this->assertTrue($class->hasMethod('getId'));
        $this->assertTrue($class->hasProperty('user'));
        $this->assertTrue($class->hasMethod('getUser'));


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
        $this->assertEquals(1, count($create));
        $this->assertContains('create table', $create[0], '', true);
        $this->assertContains('ExpressUsers', $create[0]);
        $this->assertContains('id', $create[0]);
        $this->assertContains('userFavoriteCollectionId', $create[0]);
        $this->assertContains('auto_increment', $create[0], '', true);
        $this->assertContains('unique index', $create[0], '', true);

        $manager = new \Concrete\Core\Express\SchemaManager($factory);
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('UserFavoriteCollection');
        $create = $manager->getCreateSql($entity);

        $this->assertEquals(1, count($create));

        $this->assertContains('create table', $create[0], '', true);
        $this->assertContains('ExpressUserFavoriteCollections', $create[0]);
        $this->assertContains('id', $create[0]);
        $this->assertNotContains('unique index', $create[0], '', true);

    }


}
