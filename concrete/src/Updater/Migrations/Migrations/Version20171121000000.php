<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171121000000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
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

    public function postUp(Schema $schema)
    {
        $db = $this->connection;
        $db->executeQuery("UPDATE FileImageThumbnailPaths SET thumbnailFormat = ? WHERE thumbnailFormat = '' AND (path LIKE '%.jpg' OR path LIKE '%.jpeg' OR path LIKE '%.pjpg' OR path LIKE '%.pjpeg')", [ThumbnailFormatService::FORMAT_JPEG]);
        $db->executeQuery("UPDATE FileImageThumbnailPaths SET thumbnailFormat = ? WHERE thumbnailFormat = ''", [ThumbnailFormatService::FORMAT_PNG]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
