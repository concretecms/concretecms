<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20170418000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            // Technically only the notification form submission entity is new but we need all of the entities
            // included so that all the foreign keys are created.
            'Concrete\Core\Entity\Notification\Notification',
            'Concrete\Core\Entity\Express\Entry',
            'Concrete\Core\Entity\Notification\NewFormSubmissionNotification',
        ]);
    }

    public function down(Schema $schema)
    {
    }
}
