<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\ORM\EntityManagerInterface;

final class Version20200523051311 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app->make(EntityManagerInterface::class);
        /** @var CategoryService $categoryService */
        $categoryService = $this->app->make(CategoryService::class);
        /** @var TypeFactory $attributeTypeFactory */
        $attributeTypeFactory = $this->app->make(TypeFactory::class);

        $categoryService->add("express", StandardSetManager::ASET_ALLOW_SINGLE);

        $category = $categoryService->getByHandle("express");

        $mappedAttributesTypeHandles = [
            "text",
            "textarea",
            "boolean",
            "date_time",
            "image_file",
            "number",
            "select",
            "address",
            "telephone",
            "url",
            "email",
            "rating",
            "topics",
            "calendar",
            "calendar_event",
            "page_selector",
            "user_selector",
            "site",
            "user_group"
        ];

        foreach ($attributeTypeFactory->getList() as $attributeType) {
            if ($attributeType instanceof Type && in_array($attributeType->getAttributeTypeHandle(), $mappedAttributesTypeHandles)) {
                $category->getAttributeTypes()->add($attributeType);
            }
        }

        $entityManager->persist($category);
        $entityManager->flush();


        $this->output(t('Updating tables found in doctrine xml...'));
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'TreeExpressEntrySiteResultNodes',
        ]);

        $this->refreshEntities([
            Entity::class,
            Entry::class,
        ]);

        $results = NodeType::getByHandle('express_entry_site_results');
        if (!is_object($results)) {
            NodeType::add('express_entry_site_results');
        }

        /**
         * @var $objectManager ObjectManager
         */
        $objectManager = $this->app->make(ObjectManager::class);
        $list = $objectManager->getEntities(true);
        $db = $this->app->make(Connection::class);
        /**
         * @var $db Connection
         */
        foreach($list->findAll() as $entity) {
            $db->executeQuery(
                'update ExpressEntityEntries set resultsNodeID = ? 
                where exEntryEntityID = ? and (resultsNodeID = 0 or resultsNodeID is null)',
            [$entity->getEntityResultsNodeId(), $entity->getId()]
            );
            $this->output(t('Updating entries to add multisite support for entity: %s', $entity->getName()));
        }

    }
}
