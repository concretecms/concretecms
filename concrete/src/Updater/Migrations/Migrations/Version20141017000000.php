<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Exception;

class Version20141017000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '5.7.1';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        /* refresh CollectionVersionBlocks, CollectionVersionBlocksCacheSettings tables */
        $cvb = $schema->getTable('CollectionVersionBlocks');
        if (!$cvb->hasColumn('cbOverrideBlockTypeCacheSettings')) {
            $cvb->addColumn('cbOverrideBlockTypeCacheSettings', 'boolean', ['default' => 0]);
        }

        if (!$schema->hasTable('CollectionVersionBlocksCacheSettings')) {
            $cvbcs = $schema->createTable('CollectionVersionBlocksCacheSettings');
            $cvbcs->addColumn('cID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => 0]);
            $cvbcs->addColumn('cvID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => 1]);
            $cvbcs->addColumn('bID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => 0]);
            $cvbcs->addColumn('arHandle', 'string', ['notnull' => false]);
            $cvbcs->addColumn('btCacheBlockOutput', 'boolean', ['default' => 0]);
            $cvbcs->addColumn('btCacheBlockOutputOnPost', 'boolean', ['default' => 0]);
            $cvbcs->addColumn('btCacheBlockOutputForRegisteredUsers', 'boolean', ['default' => 0]);
            $cvbcs->addColumn('btCacheBlockOutputLifetime', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => 0]);
            $cvbcs->setPrimaryKey(['cID', 'cvID', 'bId', 'arHandle']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        /* add permissions lines for edit_block_name and edit_block_cache_settings */
        $ebk = Key::getByHandle('edit_block_name');
        if (!is_object($ebk)) {
            Key::add('block', 'edit_block_name', 'Edit Name', "Controls whether users can change the block's name (rarely used.).", false, false);
        }
        $ebk = Key::getByHandle('edit_block_cache_settings');
        if (!is_object($ebk)) {
            Key::add('block', 'edit_block_cache_settings', 'Edit Cache Settings', 'Controls whether users can change the block cache settings for this block instance.', false, false);
        }

        /* Add marketplace single pages */
        $this->createSinglePage('/dashboard/extend/connect', 'Connect to the Community', ['meta_keywords' => 'concrete5.org, my account, marketplace']);
        $this->createSinglePage('/dashboard/extend/themes', 'Get More Themes', ['meta_keywords' => 'buy theme, new theme, marketplace, template']);
        $this->createSinglePage('/dashboard/extend/addons', 'Get More Add-Ons', ['meta_keywords' => 'buy addon, buy add on, buy add-on, purchase addon, purchase add on, purchase add-on, find addon, new addon, marketplace']);

        /* Add auth types ("handle|name") "twitter|Twitter" and "community|concrete5.org" */
        try {
            $community = AuthenticationType::getByHandle('community');
        } catch (Exception $e) {
            $community = AuthenticationType::add('community', 'concrete5.org');
            if (is_object($community)) {
                $community->disable();
            }
        }

        try {
            $twitter = AuthenticationType::getByHandle('twitter');
        } catch (Exception $e) {
            $twitter = AuthenticationType::add('twitter', 'Twitter');
            if (is_object($twitter)) {
                $twitter->disable();
            }
        }

        /* delete customize page themes dashboard single page */
        $customize = Page::getByPath('/dashboard/pages/themes/customize');
        if (is_object($customize) && !$customize->isError()) {
            $customize->delete();
        }

        /* exclude nav from flat view in dashboard */
        $flat = Page::getByPath('/dashboard/sitemap/explore');
        if (is_object($customize) && !$customize->isError() && $this->isAttributeHandleValid(PageCategory::class, 'exclude_nav')) {
            $flat->setAttribute('exclude_nav', false);
        }
    }
}
