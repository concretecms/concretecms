<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170626000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->refreshEntities([
            'Concrete\Core\Entity\Notification\NewFormSubmissionNotification',
            'Concrete\Core\Entity\Express\Entry',
        ]);
    }

    public function down(Schema $schema)
    {
    }
}
