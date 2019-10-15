<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * This migration updates topic category names imported from 5.x.
 */
class Version20191015060417 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgradeDatabase()
    {
        $sm = $this->connection->getSchemaManager();
        if ($sm->tablesExist(['TreeCategoryNodes'])) {
            $sm->renameTable('TreeCategoryNodes', '_TreeCategoryNodes');
            $this->output(t('Updating Category Names...'));
            $categories = $this->connection->fetchAll('select * from _TreeCategoryNodes');
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder->update('TreeNodes')->set('treeNodeName', ':nodeName')
                ->where('treeNodeID = :nodeID')
                ->andWhere($queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull('treeNodeName'),
                    $queryBuilder->expr()->eq('treeNodeName', '\'\'')
                ));
            foreach ($categories as $category) {
                $queryBuilder->setParameters([
                        'nodeID' => $category['treeNodeID'],
                        'nodeName' => $category['treeNodeCategoryName'],
                    ]
                );
                $queryBuilder->execute();
            }
        }
    }
}
