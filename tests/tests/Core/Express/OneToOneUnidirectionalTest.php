<?php
use Concrete\Core\Express\ObjectBuilder;

require_once __DIR__ . "/ObjectBuilderTestTrait.php";
require_once __DIR__ . "/ExpressEntityManagerTestCaseTrait.php";

class OneToOneUnidirectionalTest extends PHPUnit_Framework_TestCase
{

    use \ExpressEntityManagerTestCaseTrait;

    protected function getEntityManager()
    {
        $user = new \Concrete\Core\Entity\Express\Entity();
        $user->setTableName('Users');
        $user->setName('User');

        $address = new \Concrete\Core\Entity\Express\Entity();
        $address->setTableName('Addresses');
        $address->setName('Address');

        $builder = Core::make('express.builder.association');
        $builder->addOneToOneUnidirectional($user, $address, 'shipping_address');

        return $this->deliverEntityManager(array($user, $address));

    }
    public function tearDown()
    {
        parent::tearDown();
        if (file_exists(__DIR__ . '/Shopping/User.php')) {
            unlink(__DIR__ . '/Shopping/User.php');
        }
        if (file_exists(__DIR__ . '/Shopping/Address.php')) {
            unlink(__DIR__ . '/Shopping/Address.php');
        }
        if (is_dir(__DIR__ . '/Shopping')) {
            rmdir(__DIR__ . '/Shopping');
        }
    }


    public function testOutputClassWritingAndNamespace()
    {

        $writer = Core::make('express.writer');
        $writer->setNamespace('Shopping');
        $writer->setEntityManager($this->getEntityManager());
        $writer->setOutputPath(__DIR__);

        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('User');
        $entity->setTableName('Users');
        $writer->writeClass($entity);

        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Address');
        $entity->setTableName('Addresses');
        $writer->writeClass($entity);

        $this->assertFileExists(__DIR__ . '/Shopping/User.php');
        require_once __DIR__ . '/Shopping/User.php';

        $this->assertTrue(class_exists('\Shopping\User', false));
        $class = new ReflectionClass('\Shopping\User');
        $this->assertTrue($class->isSubclassOf('\Concrete\Core\Express\BaseEntity'));
        $this->assertTrue($class->hasMethod('getId'));
        $this->assertTrue($class->hasProperty('shipping_address'));
        $this->assertTrue($class->hasMethod('getShippingAddress'));


        $this->assertFileExists(__DIR__ . '/Shopping/Address.php');
        require_once __DIR__ . '/Shopping/Address.php';

        $this->assertTrue(class_exists('\Shopping\Address', false));
        $class = new ReflectionClass('\Shopping\Address');
        $this->assertTrue($class->isSubclassOf('\Concrete\Core\Express\BaseEntity'));
        $this->assertTrue($class->hasMethod('getId'));
        $this->assertFalse($class->hasProperty('user'));
        $this->assertFalse($class->hasMethod('getUsers'));


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
        $this->assertContains('shippingAddressId', $create[0]);
        $this->assertContains('auto_increment', $create[0], '', true);
        $this->assertContains('unique index', $create[0], '', true);

        $manager = new \Concrete\Core\Express\SchemaManager($factory);
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Address');
        $create = $manager->getCreateSql($entity);

        $this->assertEquals(1, count($create));

        $this->assertContains('create table', $create[0], '', true);
        $this->assertContains('ExpressAddresses', $create[0]);
        $this->assertContains('id', $create[0]);
        $this->assertNotContains('unique index', $create[0], '', true);

    }


}
