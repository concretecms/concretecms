<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20190225000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @param Schema $schema
     */
    public function upgradeSchema(Schema $schema)
    {
        $table = $schema->getTable('FileImageThumbnailTypes');
        if (!$table->hasColumn('ftSaveAreaBackgroundColor')) {
            $table->addColumn('ftSaveAreaBackgroundColor', 'string', ['notnull' => true, 'default' => '']);
        }
    }
}
