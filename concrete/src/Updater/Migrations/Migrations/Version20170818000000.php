<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170818000000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            Locale::class,
        ]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
