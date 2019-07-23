<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Class Version20170316000000.
 *
 * @package Concrete\Core\Updater\Migrations\Migrations
 *
 * This migration is to find all entity search columns and refresh the database schema due to core changes that allows
 * search columns to be longer than 255 chars.
 * After refreshing all columns we go though all entities and re-fill the search indexes so that any values that are
 * longer than 255 chars will now be indexed properly.
 */
class Version20170316000000 extends AbstractMigration implements RepeatableMigrationInterface, LongRunningMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            Entity::class,
        ]);

        // Get all the entities ALL THE ENTITIES
        $entities = \Core::make('express')->getEntities(true)->findAll();
        foreach ($entities as $entity) {
            /* @var $entity \Concrete\Core\Entity\Express\Entity */
            $category = $entity->getAttributeKeyCategory();
            // Get all the attributes for the entity
            foreach ($entity->getAttributes() as $key) {
                // Refresh the column from the attribute's search index definition if we can
                // (we don't require this method in the interface yet)
                try {
                    $category->getSearchIndexer()->refreshRepositoryColumns($category, $key);
                } catch (\Exception $e) {
                }
            }
            // Get a list of the entities
            $list = new EntryList($entity);
            $entries = $list->getResults();
            foreach ($entries as $entry) {
                // Get the values for the entities
                $values = $category->getAttributeValues($entry);
                foreach ($values as $value) {
                    // Set the values in the search index to the values of the actual attribute
                    $category->getSearchIndexer()->indexEntry($category, $value, $entry);
                }
            }
        }
    }
}
