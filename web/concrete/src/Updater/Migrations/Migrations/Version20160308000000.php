<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160308000000 extends AbstractMigration
{

    protected function renameProblematicTables()
    {
        if (!$this->connection->tableExists('_AttributeKeys')) {
            $this->connection->Execute('alter table AttributeKeys rename _AttributeKeys');
        }
        if (!$this->connection->tableExists('_AttributeValues')) {
            $this->connection->Execute('alter table AttributeValues rename _AttributeValues');
        }
        if (!$this->connection->tableExists('_CollectionAttributeValues')) {
            $this->connection->Execute('alter table CollectionAttributeValues rename _CollectionAttributeValues');
        }
        if (!$this->connection->tableExists('_FileAttributeValues')) {
            $this->connection->Execute('alter table FileAttributeValues rename _FileAttributeValues');
        }
        if (!$this->connection->tableExists('_UserAttributeValues')) {
            $this->connection->Execute('alter table UserAttributeValues rename _UserAttributeValues');
        }
    }

    protected function updateDoctrineXmlTables()
    {
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'CollectionVersions',
            'TreeNodes',
            'Sessions',
            'UserWorkflowProgress',
            'Users',
        ));
    }

    protected function installEntities()
    {
        // Add tables for new entities or moved entities
        $sm = \Core::make('Concrete\Core\Database\DatabaseStructureManager');
        $entities = array();

        // Now we fill the rest of the class names recursively from the entity directory, since it's
        // entirely new
        $entityPath = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Entity';
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($entityPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path) {
            if (!$path->isDir()) {
                $path = $path->__toString();
                if (substr(basename($path), 0, 1) != '.') {
                    $path = str_replace(array($entityPath, '.php'), '', $path);
                    $entities[] = 'Concrete\Core\Entity' . str_replace('/', '\\', $path);
                }
            }
        }

        $em = $this->connection->getEntityManager();
        $cmf = $em->getMetadataFactory();
        $metadatas = array();
        foreach ($cmf->getAllMetadata() as $meta) {
            if (in_array($meta->getName(), $entities)) {
                $metadatas[] = $meta;
            }
        }

        $sm->installDatabaseFor($metadatas);

    }

    protected function importAttributeKeys()
    {
        // loop through all old attributes and make sure to import them into the new system
        $r = $this->connection->executeQuery('select ak.*, akCategoryHandle from _AttributeKeys ak inner join AttributeKeyCategories akc on ak.akCategoryID = akc.akCategoryID;');
        while ($row = $r->fetch()) {
            $table = false;
            $akCategory = null;
            switch($row['akCategoryHandle']) {
                case 'collection':
                    $table = 'CollectionAttributeKeys';
                    $akCategory = 'pagekey';
                    break;
                case 'file':
                    $table = 'FileAttributeKeys';
                    $akCategory = 'filekey';
                    break;
                case 'user':
                    $akCategory = 'userkey';
                    break;
            }
            $data = array(
                'akID' => $row['akID'],
                'akName' => $row['akName'],
                'akHandle' => $row['akHandle'],
                'akIsSearchable' => $row['akIsSearchable'],
                'akIsSearchableIndexed' => $row['akIsSearchableIndexed'],
                'akIsInternal' => $row['akIsInternal'],
                'akIsColumnHeader' => $row['akIsColumnHeader'],
                'pkgID' => $row['pkgID'],
                'akCategory' => $akCategory,
            );
            $this->connection->insert('AttributeKeys', $data);
            if ($table) {
                $count = $this->connection->fetchColumn("select count(*) from {$table} where akID = ?", array($row['akID']));
                if (!$count) {
                    $this->connection->insert($table, array('akID' => $row['akID']));
                }
            }

            $this->importAttributeKeyType($row['atID'], $row['akID']);
        }
    }

    protected function importAttributeKeyType($atID, $akID)
    {
        $row = $this->connection->fetchAssoc('select * from AttributeTypes where atID = ?', array($atID));
        if ($row['atID']) {
            $count = $this->connection->fetchColumn("select count(*) from AttributeKeyTypes where akID = ?", array($akID));
            $type = strtolower(preg_replace("/[^A-Za-z]/", '', $row['atHandle'])) . 'type';
            if (!$count) {
                $this->connection->insert('AttributeKeyTypes', ['akTypeHandle' => $row['atHandle'], 'akID' => $akID, $type]);
            }
        }
    }

    protected function importAttributeValues()
    {

    }



    public function up(Schema $schema)
    {
        $this->connection->Execute('set foreign_key_checks = 0');
        $this->renameProblematicTables();
        $this->updateDoctrineXmlTables();
        $this->installEntities();
        $this->importAttributeKeys();
        $this->importAttributeKeyTypes();
        $this->importAttributeValues();
        $this->connection->Execute('set foreign_key_checks = 1');

    }

    public function down(Schema $schema)
    {
    }
}
