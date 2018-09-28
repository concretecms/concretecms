<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\ORM\EntityManager;

class Version20180122213656 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '8.3.2';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $app = Facade::getFacadeApplication();
        $categoryService = $app->make(CategoryService::class);
        /* @var CategoryService $categoryService */
        $typeFactory = $app->make(TypeFactory::class);
        /* @var TypeFactory $typeFactory */
        $em = $app->make(EntityManager::class);

        $type = $typeFactory->getByHandle('express');
        if (!$type) {
            $type = $typeFactory->add('express', 'Express Entity');
        }

        foreach (['collection', 'user', 'file', 'site'] as $handle) {
            $category = $categoryService->getByHandle($handle);
            if ($category !== null) {
                $categoryTypes = $category->getAttributeTypes();
                if (!$categoryTypes->contains($type)) {
                    $categoryTypes->add($type);
                }
            }
        }
        $em->flush();
    }
}
