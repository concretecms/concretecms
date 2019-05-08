<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\ORM\EntityManager;

class Version20190504005632 extends AbstractMigration implements RepeatableMigrationInterface
{

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

        $type = $typeFactory->getByHandle('user_group');
        if (!$type) {
            $type = $typeFactory->add('user_group', 'User Group');
        }

        foreach (['site', 'site_type'] as $handle) {
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
