<?php

use \Concrete\Core\File\StorageLocation\Type\Type;
use \Concrete\Core\File\StorageLocation\StorageLocation;

abstract class FileStorageTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array(
        'FileStorageLocationTypes',
        'FileImageThumbnailTypes',
    );

    protected $metadatas = array(
        'Concrete\Core\File\File',
        'Concrete\Core\File\Version',
        'Concrete\Core\File\StorageLocation\StorageLocation'
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
     * @return \Concrete\Core\File\StorageLocation\StorageLocation
     */
    protected function getStorageLocation()
    {
        $type = Type::add('local', t('Local Storage'));
        $configuration = $type->getConfigurationObject();
        $configuration->setRootPath($this->getStorageDirectory());
        $configuration->setWebRootRelativePath('/application/files');

        return StorageLocation::add($configuration, 'Default', true);
    }

    protected function setUp()
    {
        parent::setUp();

        $thumbnailType = new \Concrete\Core\File\Image\Thumbnail\Type\Type();
        $thumbnailType->requireType();
        $thumbnailType->setName(t('File Manager Thumbnails'));
        $thumbnailType->setHandle(Config::get('concrete.icons.file_manager_listing.handle'));
        $thumbnailType->setWidth(Config::get('concrete.icons.file_manager_listing.width'));
        $thumbnailType->setHeight(Config::get('concrete.icons.file_manager_listing.height'));
        $thumbnailType->save();

        $thumbnailType = new \Concrete\Core\File\Image\Thumbnail\Type\Type();
        $thumbnailType->requireType();
        $thumbnailType->setName(t('File Manager Detail Thumbnails'));
        $thumbnailType->setHandle(Config::get('concrete.icons.file_manager_detail.handle'));
        $thumbnailType->setWidth(Config::get('concrete.icons.file_manager_detail.width'));
        $thumbnailType->save();

        $this->cleanup();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->cleanup();
    }
}
