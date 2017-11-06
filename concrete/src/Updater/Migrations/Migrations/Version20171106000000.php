<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Express\Entity as ExpressEntity;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171106000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->fixAttributeIndexTable($schema, 'FileSearchIndexAttributes', 'fID', 'Files', 'fID');
        $this->fixAttributeIndexTable($schema, 'CollectionSearchIndexAttributes', 'cID', 'Collections', 'cID');
        $this->fixAttributeIndexTable($schema, 'SiteSearchIndexAttributes', 'siteID', 'Sites', 'siteID');
        $this->fixAttributeIndexTable($schema, 'UserSearchIndexAttributes', 'uID', 'Users', 'uID');
        $entityManager = $this->connection->getEntityManager();
        $expressEntityRepository = $entityManager->getRepository(ExpressEntity::class);
        foreach ($expressEntityRepository->findAll() as $expressEntity) {
            $this->fixExpressEntityAttributeIndexTable($schema, $expressEntity);
        }
    }

    public function down(Schema $schema)
    {
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
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @param \Concrete\Core\Entity\Express\Entity $expressEntity
     */
    private function fixExpressEntityAttributeIndexTable(Schema $schema, ExpressEntity $expressEntity)
    {
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
