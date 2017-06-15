<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20170412000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->Execute('set foreign_key_checks = 0');
        $this->refreshEntities([
            'Concrete\Core\Entity\Attribute\Value\Value\AbstractValue',
            'Concrete\Core\Entity\Attribute\Value\Value\AddressValue',
            'Concrete\Core\Entity\Attribute\Value\Value\BooleanValue',
            'Concrete\Core\Entity\Attribute\Value\Value\DateTimeValue',
            'Concrete\Core\Entity\Attribute\Value\Value\ExpressValue',
            'Concrete\Core\Entity\Attribute\Value\Value\ImageFileValue',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectValue',
            'Concrete\Core\Entity\Attribute\Value\Value\SocialLinksValue',
            'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
            'Concrete\Core\Entity\Attribute\Value\Value\TopicsValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\File\File',
            'Concrete\Core\Entity\Attribute\Key\Key'
        ]);
        $this->connection->Execute('set foreign_key_checks = 1');
    }

    public function down(Schema $schema)
    {
    }
}
