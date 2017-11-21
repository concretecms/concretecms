<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171012000000 extends AbstractMigration
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
