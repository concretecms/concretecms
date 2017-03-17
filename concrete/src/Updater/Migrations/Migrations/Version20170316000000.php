<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Express\EntryList;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170316000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $entities = \Core::make('express')->getEntities(true)->findAll();
        foreach ($entities as $entity) {
            /* @var $entity \Concrete\Core\Entity\Express\Entity */
            $category = $entity->getAttributeKeyCategory();
            foreach ($entity->getAttributes() as $key) {
                $category->getSearchIndexer()->refreshRepositoryColumns($category, $key);
            }
            $list = new EntryList($entity);
            $entries = $list->getResults();
            foreach($entries as $entry) {
                $values = $category->getAttributeValues($entry);
                foreach ($values as $value) {
                    $category->getSearchIndexer()->indexEntry($category, $value, $entry);
                }
            }
        }
    }

    public function down(Schema $schema)
    {
    }
}
