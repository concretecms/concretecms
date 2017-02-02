<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;

class Version20170202000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            DateTimeSettings::class,
        ]);
    }

    public function down(Schema $schema)
    {
    }
}
