<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SinglePage;
use Exception;

class Version571 extends AbstractMigration
{

    public function getName()
    {
        return '20141017000000';
    }

    public function up(Schema $schema)
    {
        /** refresh CollectionVersionBlocks, CollectionVersionBlocksCacheSettings tables */
        $cvb = $schema->getTable('CollectionVersionBlocks');
        $cvb->addColumn('cbOverrideBlockTypeCacheSettings', 'boolean', array('default' => 0));

        $cvbcs = $schema->createTable('CollectionVersionBlocksCacheSettings');
        $cvbcs->addColumn('cID', 'integer', array('notnull' => true, 'unsigned' => true, 'default' => 0));
        $cvbcs->addColumn('cvID', 'integer', array('notnull' => true, 'unsigned' => true, 'default' => 1));
        $cvbcs->addColumn('bID', 'integer', array('notnull' => true, 'unsigned' => true, 'default' => 0));
        $cvbcs->addColumn('arHandle', 'string', array('notnull' => false));
        $cvbcs->addColumn('btCacheBlockOutput', 'boolean', array('default' => 0));
        $cvbcs->addColumn('btCacheBlockOutputOnPost', 'boolean', array('default' => 0));
        $cvbcs->addColumn('btCacheBlockOutputForRegisteredUsers', 'boolean', array('default' => 0));
        $cvbcs->addColumn('btCacheBlockOutputLifetime', 'integer', array('notnull' => true, 'unsigned' => true, 'default' => 0));
        $cvbcs->setPrimaryKey(array('cID', 'cvID', 'bId', 'arHandle'));

        /** add permissions lines for edit_block_name and edit_block_cache_settings */
		$ebk = Key::getByHandle('edit_block_name');
		if (!is_object($ebk)) {
			Key::add('block', 'edit_block_name', 'Edit Name', "Controls whether users can change the block's name (rarely used.).", false, false);
		}
		$ebk = Key::getByHandle('edit_block_cache_settings');
		if (!is_object($ebk)) {
			Key::add('block', 'edit_block_cache_settings', 'Edit Cache Settings', "Controls whether users can change the block cache settings for this block instance.", false, false);
		}

        /** Add marketplace single pages */
		$sp = Page::getByPath('/dashboard/extend/connect');
		if (!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/extend/connect');
			$sp->update(array('cName' => 'Connect to the Community'));
			$sp->setAttribute('meta_keywords', 'concrete5.org, my account, marketplace');
		}
		$sp = Page::getByPath('/dashboard/extend/themes');
		if (!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/extend/themes');
			$sp->update(array('cName' => 'Get More Themes'));
			$sp->setAttribute('meta_keywords', 'buy theme, new theme, marketplace, template');
		}
		$sp = Page::getByPath('/dashboard/extend/addons');
		if (!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/extend/addons');
			$sp->update(array('cName' => 'Get More Add-Ons'));
			$sp->setAttribute('meta_keywords', 'buy addon, buy add on, buy add-on, purchase addon, purchase add on, purchase add-on, find addon, new addon, marketplace');
		}

        /** Add auth types ("handle|name") "twitter|Twitter" and "community|concrete5.org" */
        try {
            $community = AuthenticationType::getByHandle('community');
        } catch(Exception $e) {
            $community = AuthenticationType::add('community', 'concrete5.org');
            if (is_object($community)) {
                $community->disable();
            }
        }

        try {
            $twitter = AuthenticationType::getByHandle('twitter');
        } catch(Exception $e) {
            $twitter = AuthenticationType::add('twitter', 'Twitter');
            if (is_object($twitter)) {
                $twitter->disable();
            }
        }

        /** delete customize page themes dashboard single page */
        $customize = Page::getByPath('/dashboard/pages/themes/customize');
        if (is_object($customize) && !$customize->isError()) {
            $customize->delete();
        }

        /** exclude nav from flat view in dashboard */
        $flat = Page::getByPath('/dashboard/sitemap/explore');
        if (is_object($customize) && !$customize->isError()) {
            $flat->setAttribute("exclude_nav", false);
        }
    }

    public function down(Schema $schema)
    {
    }
}
