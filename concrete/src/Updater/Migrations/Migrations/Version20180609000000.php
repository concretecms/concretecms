<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Attribute\Key\Category;
use Doctrine\ORM\EntityManager;

class Version20180609000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $factory = $this->app->make(TypeFactory::class);

        // check if an attribute with the 'page_selector' handle exists
        $pageSelectorAttribute = $factory->getByHandle('page_selector');
        if (is_object($pageSelectorAttribute)) {

            // unlink the attribute type from any package and change its name
            $pageSelectorAttribute->setPackage(null);
            $pageSelectorAttribute->setAttributeTypeName(t('Page Selector'));
            $em = $this->app->make(EntityManager::class);
            $em->persist($pageSelectorAttribute);
            $em->flush();
        } else {

            // add the 'page_selector' attribute
            $type = $factory->add('page_selector', t('Page Selector'));

            // associate the 'page_selector' attribute to collection category
            $category = Category::getByHandle('collection')->getController();
            $category->associateAttributeKeyType($type);

            // associate the 'page_selector' attribute to site category
            $category = Category::getByHandle('site')->getController();
            $category->associateAttributeKeyType($type);
        }
    }
}
