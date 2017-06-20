<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170608100000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            'Concrete\Core\Entity\Attribute\Key\Settings\TopicsSettings',
        ]);
    }

    public function down(Schema $schema)
    {
    }
}
