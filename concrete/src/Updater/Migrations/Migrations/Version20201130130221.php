<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\ORM\EntityManagerInterface;

final class Version20201130130221 extends AbstractMigration implements RepeatableMigrationInterface
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
            "express",
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
    }
}
