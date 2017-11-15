<?php
namespace Concrete\Tests\Core\File\StorageLocation;

use Concrete\Core\Entity\File\StorageLocation\Type\Type as StorageLocationType;
use Concrete\Core\File\StorageLocation\Type\Type;
use Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration;
use Concrete\Core\Http\Request;

class ConfigurationTest extends \ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $metadatas = array(
        StorageLocationType::class,
    );

    protected function getStorageDirectory()
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__)) . '/files';
    }

    public function testConfigureType()
    {
        $type = Type::add('local', t('Local Storage'));
        $configuration = $type->getConfigurationObject();
        $configuration->setRootPath($this->getStorageDirectory());
        $this->assertInstanceOf(LocalConfiguration::class, $configuration);
        $this->assertEquals($this->getStorageDirectory(), $configuration->getRootPath());

        $req = new Request();
        $req->setMethod('POST');
        $data = array();
        $data['path'] = '/foo/bar/path';
        $req->request->set('fslType', $data);
        $configuration->loadFromRequest($req);

        $this->assertEquals('/foo/bar/path', $configuration->getRootPath());
    }
}
