<?php

namespace Concrete\TestHelpers\File;

use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\File\StorageLocation\Type\Type;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Config;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

abstract class FileStorageTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = [];
    protected $tables = [
        'Groups',
        'FileStorageLocationTypes',
        'FileImageThumbnailTypes',
        'TreeTypes',
        'TreeNodeTypes',
        'TreeNodes',
        'TreeFileFolderNodes',
        'TreeFileNodes',
        'TreeNodePermissionAssignments',
        'PermissionKeys',
        'PermissionKeyCategories',
        'Trees',
    ];

    protected $metadatas = [
        'Concrete\Core\Entity\File\File',
        'Concrete\Core\Entity\File\Version',
        'Concrete\Core\Entity\File\StorageLocation\Type\Type',
        'Concrete\Core\Entity\File\Image\Thumbnail\Type\Type',
        'Concrete\Core\Entity\File\StorageLocation\StorageLocation',
    ];

    protected function setUp()
    {
        parent::setUp();

        $filesystem = new Filesystem();
        $filesystem->create();

        $thumbnailType = new ThumbnailType();
        $thumbnailType->requireType();
        $thumbnailType->setName(t('File Manager Thumbnails'));
        $thumbnailType->setHandle(Config::get('concrete.icons.file_manager_listing.handle'));
        $thumbnailType->setWidth(Config::get('concrete.icons.file_manager_listing.width'));
        $thumbnailType->setHeight(Config::get('concrete.icons.file_manager_listing.height'));
        $thumbnailType->save();

        $thumbnailType = new ThumbnailType();
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

    protected function getStorageDirectory()
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/files';
    }

    protected function cleanup()
    {
        if (is_dir($this->getStorageDirectory())) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->getStorageDirectory(), RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }

            rmdir($this->getStorageDirectory());
        }
    }

    /**
     * @return \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    protected function getStorageLocation()
    {
        $type = Type::add('local', t('Local Storage'));
        $configuration = $type->getConfigurationObject();
        $configuration->setRootPath($this->getStorageDirectory());
        $configuration->setWebRootRelativePath('/application/files');

        return StorageLocation::add($configuration, 'Default', true);
    }
}
