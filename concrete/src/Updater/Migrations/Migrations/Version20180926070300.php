<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Express\Entity as ExpressEntity;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;

class Version20180926070300 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeSchema(Schema $schema)
    {
        $this->fixAttributeIndexTable($schema, 'FileSearchIndexAttributes', 'fID', 'Files', 'fID');
        $this->fixAttributeIndexTable($schema, 'CollectionSearchIndexAttributes', 'cID', 'Collections', 'cID');
        $this->fixAttributeIndexTable($schema, 'SiteSearchIndexAttributes', 'siteID', 'Sites', 'siteID');
        $this->fixAttributeIndexTable($schema, 'UserSearchIndexAttributes', 'uID', 'Users', 'uID');
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $expressEntityRepository = $entityManager->getRepository(ExpressEntity::class);
        foreach ($expressEntityRepository->findAll() as $expressEntity) {
            $this->fixExpressEntityAttributeIndexTable($schema, $expressEntity);
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @param string $indexTableName
     * @param string $indexColumnName
     * @param string $foreignTableName
     * @param string $foreignColumn
     */
    private function fixAttributeIndexTable(Schema $schema, $indexTableName, $indexColumnName, $foreignTableName, $foreignColumn)
    {
        $this->output("Fixing attribute index table {$indexTableName}...");
        $sm = $this->connection->getSchemaManager();
        $schemaTables = $sm->listTableNames();
        if (in_array($indexTableName, $schemaTables)) {
            $this->deleteInvalidForeignKey($indexTableName, $indexColumnName, $foreignTableName, $foreignColumn);
            $indexTable = $schema->getTable($indexTableName);
            $indexColumn = $indexTable->getColumn($indexColumnName);
            $indexColumn->setDefault(null);
            $indexTable->addForeignKeyConstraint(
                $foreignTableName,
                [$indexColumnName],
                [$foreignColumn],
                ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE']
            );
        } else {
            $this->output(t('Could not locate table %s, skipping...', $indexTableName));
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @param \Concrete\Core\Entity\Express\Entity $expressEntity
     */
    private function fixExpressEntityAttributeIndexTable(Schema $schema, ExpressEntity $expressEntity)
    {
        $this->output('Fixing express index table for ' . $expressEntity->getName() . '...');
        $attributeCategory = $expressEntity->getAttributeKeyCategory();
        /* @var \Concrete\Core\Attribute\Category\ExpressCategory $attributeCategory */
        $indexTableName = $attributeCategory->getIndexedSearchTable();
        $indexTable = $schema->getTable($indexTableName);
        $exEntryIDColumn = $indexTable->getColumn('exEntryID');
        $exEntryIDColumn->setUnsigned(false);
        $this->fixAttributeIndexTable(
            $schema,
            $indexTableName, 'exEntryID',
            'ExpressEntityEntries', 'exEntryID'
        );
    }
}
