<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Attribute\Key\Category;
use Doctrine\ORM\EntityManager;

class Version20180926000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $factory = $this->app->make(TypeFactory::class);

        // check if an attribute with the 'user_selector' handle exists
        $userSelectorAttribute = $factory->getByHandle('user_selector');
        if (is_object($userSelectorAttribute)) {

            // unlink the attribute type from any package and change its name
            $userSelectorAttribute->setPackage(null);
            $userSelectorAttribute->setAttributeTypeName(t('User Selector'));
            $em = $this->app->make(EntityManager::class);
            $em->persist($userSelectorAttribute);
            $em->flush();
        } else {

            // add the 'user_selector' attribute
            $type = $factory->add('user_selector', t('User Selector'));

            // associate the 'user_selector' attribute to collection category
            $category = Category::getByHandle('collection')->getController();
            $category->associateAttributeKeyType($type);

            // associate the 'user_selector' attribute to site category
            $category = Category::getByHandle('site')->getController();
            $category->associateAttributeKeyType($type);

            // associate the 'user_selector' attribute to user category
            $category = Category::getByHandle('user')->getController();
            $category->associateAttributeKeyType($type);

            // associate the 'user_selector' attribute to file category
            $category = Category::getByHandle('file')->getController();
            $category->associateAttributeKeyType($type);

            // associate the 'user_selector' attribute to file category
            $category = Category::getByHandle('event')->getController();
            $category->associateAttributeKeyType($type);
        }

        $db = $this->connection;
        if ($db->tableExists('atUserSelector')) {
            // This is the name of the user selector attribute table in some implementations of the user selector attribute
            // We need to take this data and place it into atNumber.
            $db->query(<<<EOT
insert into atNumber (avID, value)
    select
        atUserSelector.avID, atUserSelector.value
    from
        atUserSelector
    inner join
        AttributeValues on atUserSelector.avID = AttributeValues.avID
    left join
        atNumber on atUserSelector.avID = atNumber.avID
    where
        atNumber.avID is null
EOT
            );
        }
    }
}
