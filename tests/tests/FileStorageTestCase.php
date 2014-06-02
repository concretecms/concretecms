<?php

use \Concrete\Core\File\StorageLocation\Type\Type;
use \Concrete\Core\File\StorageLocation\StorageLocation;

abstract class FileStorageTestCase extends ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array(
        'FileStorageLocationTypes',
        'FileStorageLocations',
    );

    protected function getStorageDirectory()
    {
        return dirname(__FILE__) . '/files';
    }

    protected function cleanup()
    {
        if (is_dir($this->getStorageDirectory())) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->getStorageDirectory(), \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }

            rmdir($this->getStorageDirectory());
        }
    }

    /**
     * @return \Gaufrette\Filesystem
     */
    protected function getStorageLocation()
    {
        $type = Type::add('local', t('Local Storage'));
        $configuration = $type->getConfigurationObject();
        $configuration->setRootPath($this->getStorageDirectory());
        return StorageLocation::add($configuration, 'Default', true);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->cleanup();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->cleanup();
    }

}