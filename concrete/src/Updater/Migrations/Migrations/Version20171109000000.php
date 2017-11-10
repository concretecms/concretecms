<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;
use Concrete\Core\Support\Facade\Package;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171109000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshBlockType('image');
    }

    public function down(Schema $schema)
    {
    }
}
