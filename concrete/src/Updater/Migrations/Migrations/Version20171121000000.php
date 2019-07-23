<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20171121000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        $table = $schema->getTable('FileImageThumbnailPaths');
        if ($table->hasIndex('thumbnailPathID')) {
            $table->dropIndex('thumbnailPathID');
        }
        if (!$table->hasColumn('thumbnailFormat')) {
            $table->addColumn(
                'thumbnailFormat',
                'string',
                [
                    'notnull' => true,
                    'default' => '',
                    'length' => 5,
                ]
            );
            $table->setPrimaryKey([
                'fileID',
                'fileVersionID',
                'thumbnailTypeHandle',
                'storageLocationID',
                'thumbnailFormat',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $db = $this->connection;
        $db->executeQuery("UPDATE FileImageThumbnailPaths SET thumbnailFormat = ? WHERE thumbnailFormat = '' AND (path LIKE '%.jpg' OR path LIKE '%.jpeg' OR path LIKE '%.pjpg' OR path LIKE '%.pjpeg')", [BitmapFormat::FORMAT_JPEG]);
        $db->executeQuery("UPDATE FileImageThumbnailPaths SET thumbnailFormat = ? WHERE thumbnailFormat = ''", [BitmapFormat::FORMAT_PNG]);
    }
}
