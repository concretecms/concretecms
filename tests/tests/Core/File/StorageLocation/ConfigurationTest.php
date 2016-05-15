<?php
namespace Concrete\Tests\Core\File\StorageLocation;

use Concrete\Core\File\StorageLocation\Type\Type;

class ConfigurationTest extends \ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array(
        'FileStorageLocationTypes',
    );

    protected function getStorageDirectory()
    {
        return dirname(__FILE__) . '/files';
    }

    public function testConfigureType()
    {
        $type = Type::add('local', t('Local Storage'));
        $configuration = $type->getConfigurationObject();
        $configuration->setRootPath($this->getStorageDirectory());
        $this->assertInstanceOf('\Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration', $configuration);
        $this->assertEquals($this->getStorageDirectory(), $configuration->getRootPath());

        $req = new \Concrete\Core\Http\Request();
        $req->setMethod('POST');
        $data = array();
        $data['path'] = '/foo/bar/path';
        $req->request->set('fslType', $data);
        $configuration->loadFromRequest($req);

        $this->assertEquals('/foo/bar/path', $configuration->getRootPath());
    }
}
