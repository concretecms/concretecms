<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140908071333 extends AbstractMigration
{
    public function getName()
    {
        return 'Version5701';
    }

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $schema->getTable('Logs')->addColumn('testcolumn', 'string');
        $schema->getTable('Files')->changeColumn('fPassword', array('type' => \Doctrine\DBAL\Types\Type::getType('text')));
        BlockType::installBlockType('file');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->getTable('Logs')->dropColumn('testcolumn');
        $schema->getTable('Files')->changeColumn('fPassword', array('type' => \Doctrine\DBAL\Types\Type::getType('string')));
        $file = BlockType::getByHandle('file');
        $file->delete();
    }
}
