<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;
use Concrete\Core\Updater\Migrations\ManagedSchemaUpgraderInterface;
use Doctrine\DBAL\Schema\Schema;
use Exception;
use SinglePage;

class Version20141017000000 extends AbstractMigration implements ManagedSchemaUpgraderInterface, DirectSchemaUpgraderInterface
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
     * @see \Concrete\Core\Updater\Migrations\ManagedSchemaUpgraderInterface::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        /* refresh CollectionVersionBlocks, CollectionVersionBlocksCacheSettings tables */
        $cvb = $schema->getTable('CollectionVersionBlocks');
        $cvb->addColumn('cbOverrideBlockTypeCacheSettings', 'boolean', ['default' => 0]);

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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
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
        $sp = Page::getByPath('/dashboard/extend/connect');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/extend/connect');
            $sp->update(['cName' => 'Connect to the Community']);
            $sp->setAttribute('meta_keywords', 'concrete5.org, my account, marketplace');
        }
        $sp = Page::getByPath('/dashboard/extend/themes');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/extend/themes');
            $sp->update(['cName' => 'Get More Themes']);
            $sp->setAttribute('meta_keywords', 'buy theme, new theme, marketplace, template');
        }
        $sp = Page::getByPath('/dashboard/extend/addons');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/extend/addons');
            $sp->update(['cName' => 'Get More Add-Ons']);
            $sp->setAttribute('meta_keywords', 'buy addon, buy add on, buy add-on, purchase addon, purchase add on, purchase add-on, find addon, new addon, marketplace');
        }

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
        if (is_object($customize) && !$customize->isError()) {
            $flat->setAttribute('exclude_nav', false);
        }
    }
}
