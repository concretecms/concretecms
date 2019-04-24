<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Attribute\Key\SiteTypeKey;
use Concrete\Core\Entity\Attribute\Value\SiteTypeValue;
use Concrete\Core\Entity\Attribute\Value\Value\SiteValue;
use Concrete\Core\Entity\Permission\SiteGroup;
use Concrete\Core\Entity\Site\Domain;
use Concrete\Core\Entity\Site\Group\Group;
use Concrete\Core\Entity\Site\Group\Relation;
use Concrete\Core\Entity\Site\Skeleton;
use Concrete\Core\Entity\Site\SkeletonLocale;
use Concrete\Core\Entity\Site\SkeletonTree;
use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Permission\Category as PermissionKeyCategory;

final class Version20190422235040 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            SiteTypeKey::class,
            SiteTypeValue::class,
            SiteValue::class,
            SiteGroup::class,
            Domain::class,
            Group::class,
            Relation::class,
            Skeleton::class,
            SkeletonLocale::class,
            SkeletonTree::class
        ]);

        $category = Category::getByHandle('site_type');
        if (!is_object($category)) {
            $category = Category::add('site_type');
        } else {
            $category = $category->getController();
        }

        $factory = $this->app->make(TypeFactory::class);
        $types = $factory->getList();
        foreach ($types as $type) {
            $category->associateAttributeKeyType($type);
        }

        $siteAttribute = $factory->getByHandle('site');
        if (!$siteAttribute) {
            $siteAttribute = $factory->add('site', t('Site'));
        }
        foreach(['collection', 'user', 'file'] as $categoryHandle) {
            $category = Category::getByHandle($categoryHandle);
            if ($category) {
                $category->getController()->associateAttributeKeyType($siteAttribute);
            }
        }

        $siteGroupEntity = Type::getByHandle('site_group');
        if (!$siteGroupEntity) {
            $siteGroupEntity = Type::add('site_group', 'Site Group');
        }
        foreach(['page', 'page_type', 'basic_workflow'] as $categoryHandle) {
            $category = PermissionKeyCategory::getByHandle($categoryHandle);
            if ($category) {
                $category->associateAccessEntityType($siteGroupEntity);
            }
        }

        $this->createSinglePage('/dashboard/system/multisite', 'Multiple Site Hosting');
        $this->createSinglePage('/dashboard/system/multisite/sites', 'Sites &amp; Domains');
        $this->createSinglePage('/dashboard/system/multisite/types', 'Site Types');
        $this->createSinglePage('/dashboard/system/multisite/settings', 'Multisite Settings');

        // for sites with the private multisite addon installed:
        if ($this->connection->tableExists('SiteAttributeValueValues')) {
            $this->connection->query('alter table SiteAttributeValueValues rename atSite');
        }

    }
}
