<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20161203000000 extends AbstractMigration
{

    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }


    protected function fixFileFolderPermissions()
    {
        $this->output(t('Fixing file folder permissions'));
        $r = $this->connection->executeQuery('select fID from Files where folderTreeNodeID = 0');
        while ($row = $r->fetch()) {
            $properFolderID = $this->connection->fetchColumn(
                'select treeNodeID from TreeFileNodes where fID = ?', [$row['fID']]
            );
            if ($properFolderID) {
                $r = $this->connection->executeQuery(
                    'update Files set folderTreeNodeID = ? where fID = ?', [$properFolderID, $row['fID']]
                );
            }
        }
    }

    public function up(Schema $schema)
    {
        $this->fixFileFolderPermissions();
    }

    public function down(Schema $schema)
    {
    }
}
