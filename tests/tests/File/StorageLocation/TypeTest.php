<?php

namespace Concrete\Tests\Core\File\StorageLocation;

use Concrete\Core\Entity\File\StorageLocation\Type\Type as StorageLocationType;
use Concrete\Core\File\StorageLocation\Type\Type;

class TypeTest extends \ConcreteDatabaseTestCase
{
    protected $fixtures = [];
    protected $metadatas = [
        StorageLocationType::class,
    ];

    public function testCreateType()
    {
        $type = Type::add('local', t('Local Storage'));
        $this->assertInstanceOf(StorageLocationType::class, $type);
        $this->assertEquals('Local Storage', $type->getName());
        $this->assertEquals(1, $type->getID());
        $this->assertEquals(0, $type->getPackageID());
        $this->assertEquals('local', $type->getHandle());
        $type2 = Type::getByHandle('local');
        $this->assertEquals($type, $type2);
    }
}
