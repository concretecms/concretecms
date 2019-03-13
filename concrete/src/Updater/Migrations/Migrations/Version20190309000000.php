<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20190309000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        // Create the TreeFileFolderNodes table
        if (!$schema->hasTable('TreeFileFolderNodes')) {
            $treeFileFolderNodesTable = $schema->createTable('TreeFileFolderNodes');
            $treeFileFolderNodesTable->addColumn('treeNodeID', 'integer', ['notnull' => true, 'unsigned' => true, 'autoincrement' => true]);
            $treeFileFolderNodesTable->addColumn('fslID', 'integer', ['notnull' => true, 'unsigned' => true]);
            $treeFileFolderNodesTable->setPrimaryKey(['treeNodeID']);
            $treeFileFolderNodesTable->addIndex(['fslID'], 'fslID');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // Add any existing folder to the default storage location
        $db = $this->connection;
        $defaultStorageLocationID = $db->fetchColumn('SELECT fslID FROM FileStorageLocations WHERE fslIsDefault = 1');
        $folderTreeNodeTypeID = $db->fetchColumn('SELECT treeNodeTypeID FROM TreeNodeTypes WHERE treeNodeTypeHandle = ?', [
            'file_folder'
        ]);
        $folders = $db->fetchAll('SELECT treeNodeID FROM TreeNodes WHERE treeNodeID NOT IN (SELECT treeNodeID FROM TreeFileFolderNodes) AND treeNodeTypeID = ?', [
            $folderTreeNodeTypeID
        ]);
        foreach ($folders as $folder) {
            $db->executeQuery('INSERT INTO TreeFileFolderNodes (treeNodeID, fslID) VALUES (?, ?)', [
                $folder['treeNodeID'],
                $defaultStorageLocationID
            ]);
        }
    }
}
