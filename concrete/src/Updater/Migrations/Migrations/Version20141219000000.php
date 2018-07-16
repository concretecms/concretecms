<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use AuthenticationType;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Conversation\FlagType\FlagType;
use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Exception;
use Page;

class Version20141219000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '5.7.3';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        // add new multilingual tables.
        if (!$schema->hasTable('MultilingualPageRelations')) {
            $mpr = $schema->createTable('MultilingualPageRelations');
            $mpr->addColumn('mpRelationID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => 0]);
            $mpr->addColumn('cID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => 0]);
            $mpr->addColumn('mpLanguage', 'string', ['notnull' => true, 'default' => '']);
            $mpr->addColumn('mpLocale', 'string', ['notnull' => true]);
            $mpr->setPrimaryKey(['mpRelationID', 'cID', 'mpLocale']);
        }
        if (!$schema->hasTable('MultilingualSections')) {
            $mus = $schema->createTable('MultilingualSections');
            $mus->addColumn('cID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => 0]);
            $mus->addColumn('msLanguage', 'string', ['notnull' => true, 'default' => '']);
            $mus->addColumn('msCountry', 'string', ['notnull' => true, 'default' => '']);
            $mus->setPrimaryKey(['cID']);
        }
        if (!$schema->hasTable('MultilingualTranslations')) {
            $mts = $schema->createTable('MultilingualTranslations');
            $mts->addColumn('mtID', 'integer', ['autoincrement' => true, 'unsigned' => true]);
            $mts->addColumn('mtSectionID', 'integer', ['unsigned' => true, 'notnull' => true, 'default' => 0]);
            $mts->addColumn('msgid', 'text', ['notnull' => false]);
            $mts->addColumn('msgstr', 'text', ['notnull' => false]);
            $mts->addColumn('context', 'text', ['notnull' => false]);
            $mts->addColumn('comments', 'text', ['notnull' => false]);
            $mts->addColumn('reference', 'text', ['notnull' => false]);
            $mts->addColumn('flags', 'text', ['notnull' => false]);
            $mts->addColumn('updated', 'datetime', ['notnull' => false]);
            $mts->setPrimaryKey(['mtID']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $ft = FlagType::getByhandle('spam');
        if (!is_object($ft)) {
            FlagType::add('spam');
        }

        $this->refreshBlockType('image_slider');

        $types = [Type::getByHandle('group'), Type::getByHandle('user'), Type::getByHandle('group_set'), Type::getByHandle('group_combination')];
        $categories = [Category::getByHandle('conversation'), Category::getByHandle('conversation_message')];
        foreach ($categories as $category) {
            foreach ($types as $pe) {
                if (is_object($category) && is_object($pe)) {
                    $category->associateAccessEntityType($pe);
                }
            }
        }

        try {
            $gat = AuthenticationType::getByHandle('google');
        } catch (Exception $e) {
            $gat = AuthenticationType::add('google', 'Google');
            if (is_object($gat)) {
                $gat->disable();
            }
        }

        // fix register page permissions
        $g1 = \Group::getByID(GUEST_GROUP_ID);
        $register = \Page::getByPath('/register', 'RECENT');
        $register->assignPermissions($g1, ['view_page']);

        // add new permissions, set it to the same value as edit page permissions on all pages.
        $epk = PermissionKey::getByHandle('edit_page_permissions');
        $msk = PermissionKey::getByHandle('edit_page_multilingual_settings');
        $ptk = PermissionKey::getByHandle('edit_page_page_type');
        if (!is_object($msk)) {
            $msk = PermissionKey::add('page', 'edit_page_multilingual_settings', 'Edit Multilingual Settings', 'Controls whether a user can see the multilingual settings menu, re-map a page or set a page as ignored in multilingual settings.', false, false);
        }
        if (!is_object($ptk)) {
            $ptk = PermissionKey::add('page', 'edit_page_page_type', 'Edit Page Type', 'Change the type of an existing page.', false, false);
        }
        $db = \Database::get();
        $r = $db->Execute('select cID from Pages where cInheritPermissionsFrom = "OVERRIDE" order by cID asc');
        while ($row = $r->FetchRow()) {
            $c = Page::getByID($row['cID']);
            if (is_object($c) && !$c->isError()) {
                $epk->setPermissionObject($c);
                $msk->setPermissionObject($c);
                $ptk->setPermissionObject($c);
                $rpa = $epk->getPermissionAccessObject();
                if (is_object($rpa)) {
                    $pt = $msk->getPermissionAssignmentObject();
                    if (is_object($pt)) {
                        $pt->clearPermissionAssignment();
                        $pt->assignPermissionAccess($rpa);
                    }
                    $pt = $ptk->getPermissionAssignmentObject();
                    if (is_object($pt)) {
                        $pt->clearPermissionAssignment();
                        $pt->assignPermissionAccess($rpa);
                    }
                }
            }
        }

        // add new page type permissions
        $epk = PermissionKey::getByHandle('edit_page_type_permissions');
        $msk = PermissionKey::getByHandle('edit_page_type');
        $dsk = PermissionKey::getByHandle('delete_page_type');
        if (!is_object($msk)) {
            $msk = PermissionKey::add('page_type', 'edit_page_type', 'Edit Page Type', '', false, false);
        }
        if (!is_object($dsk)) {
            $dsk = PermissionKey::add('page_type', 'delete_page_type', 'Delete Page Type', '', false, false);
        }
        $list = \Concrete\Core\Page\Type\Type::getList();
        foreach ($list as $pagetype) {
            $epk->setPermissionObject($pagetype);
            $msk->setPermissionObject($pagetype);
            $dsk->setPermissionObject($pagetype);
            $rpa = $epk->getPermissionAccessObject();
            if (is_object($rpa)) {
                $pt = $msk->getPermissionAssignmentObject();
                if (is_object($pt)) {
                    $pt->clearPermissionAssignment();
                    $pt->assignPermissionAccess($rpa);
                }
                $pt = $dsk->getPermissionAssignmentObject();
                if (is_object($pt)) {
                    $pt->clearPermissionAssignment();
                    $pt->assignPermissionAccess($rpa);
                }
            }
        }

        // block type
        $bt = BlockType::getByHandle('switch_language');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('switch_language');
        }

        // single pages
        $this->createSinglePage('/dashboard/system/multilingual', 'Multilingual', ['meta_keywords' => 'multilingual, localization, internationalization, i18n']);
        $this->createSinglePage('/dashboard/system/multilingual/setup', 'Multilingual Setup');
        $this->createSinglePage('/dashboard/system/multilingual/page_report', 'Page Report');
        $this->createSinglePage('/dashboard/system/multilingual/translate_interface', 'Translate Interface');
        $this->createSinglePage('/dashboard/pages/types/attributes', 'Page Type Attributes');
    }
}
