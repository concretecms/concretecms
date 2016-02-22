<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Conversation\FlagType\FlagType;
use Concrete\Core\Block\BlockType\BlockType;
use AuthenticationType;
use Exception;
use Page;
use SinglePage;
use Concrete\Core\Permission\Key\Key as PermissionKey;

class Version20141219000000 extends AbstractMigration
{
    public function getDescription()
    {
        return '5.7.3';
    }

    public function up(Schema $schema)
    {
        $ft = FlagType::getByhandle('spam');
        if (!is_object($ft)) {
            FlagType::add('spam');
        }

        $bt = BlockType::getByHandle('image_slider');
        $bt->refresh();

        $types = array(Type::getByHandle('group'), Type::getByHandle('user'), Type::getByHandle('group_set'), Type::getByHandle('group_combination'));
        $categories = array(Category::getByHandle('conversation'), Category::getByHandle('conversation_message'));
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
        $register = \Page::getByPath('/register', "RECENT");
        $register->assignPermissions($g1, array('view_page'));

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

        // add new multilingual tables.
        $sm = $db->getSchemaManager();
        $schemaTables = $sm->listTableNames();
        if (!in_array('MultilingualPageRelations', $schemaTables)) {
            $mpr = $schema->createTable('MultilingualPageRelations');
            $mpr->addColumn('mpRelationID', 'integer', array('notnull' => true, 'unsigned' => true, 'default' => 0));
            $mpr->addColumn('cID', 'integer', array('notnull' => true, 'unsigned' => true, 'default' => 0));
            $mpr->addColumn('mpLanguage', 'string', array('notnull' => true, 'default' => ''));
            $mpr->addColumn('mpLocale', 'string', array('notnull' => true));
            $mpr->setPrimaryKey(array('mpRelationID', 'cID', 'mpLocale'));
        }
        if (!in_array('MultilingualSections', $schemaTables)) {
            $mus = $schema->createTable('MultilingualSections');
            $mus->addColumn('cID', 'integer', array('notnull' => true, 'unsigned' => true, 'default' => 0));
            $mus->addColumn('msLanguage', 'string', array('notnull' => true, 'default' => ''));
            $mus->addColumn('msCountry', 'string', array('notnull' => true, 'default' => ''));
            $mus->setPrimaryKey(array('cID'));
        }
        if (!in_array('MultilingualTranslations', $schemaTables)) {
            $mts = $schema->createTable('MultilingualTranslations');
            $mts->addColumn('mtID', 'integer', array('autoincrement' => true, 'unsigned' => true));
            $mts->addColumn('mtSectionID', 'integer', array('unsigned' => true, 'notnull' => true, 'default' => 0));
            $mts->addColumn('msgid', 'text', array('notnull' => false));
            $mts->addColumn('msgstr', 'text', array('notnull' => false));
            $mts->addColumn('context', 'text', array('notnull' => false));
            $mts->addColumn('comments', 'text', array('notnull' => false));
            $mts->addColumn('reference', 'text', array('notnull' => false));
            $mts->addColumn('flags', 'text', array('notnull' => false));
            $mts->addColumn('updated', 'datetime', array('notnull' => false));
            $mts->setPrimaryKey(array('mtID'));
        }

        // block type
        $bt = BlockType::getByHandle('switch_language');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('switch_language');
        }

        // single pages
        $sp = Page::getByPath('/dashboard/system/multilingual');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/multilingual');
            $sp->update(array('cName' => 'Multilingual'));
            $sp->setAttribute('meta_keywords', 'multilingual, localization, internationalization, i18n');
        }
        $sp = Page::getByPath('/dashboard/system/multilingual/setup');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/multilingual/setup');
            $sp->update(array('cName' => 'Multilingual Setup'));
        }
        $sp = Page::getByPath('/dashboard/system/multilingual/page_report');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/multilingual/page_report');
            $sp->update(array('cName' => 'Page Report'));
        }
        $sp = Page::getByPath('/dashboard/system/multilingual/translate_interface');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/multilingual/translate_interface');
            $sp->update(array('cName' => 'Translate Interface'));
        }
        $sp = Page::getByPath('/dashboard/pages/types/attributes');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/pages/types/attributes');
            $sp->update(array('cName' => 'Page Type Attributes'));
        }
    }

    public function down(Schema $schema)
    {
    }
}
