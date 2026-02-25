<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Legacy\Loader;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Assignment\PageTimedAssignment as PageContentPermissionTimedAssignment;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Permission\Key\AreaKey as AreaPermissionKey;
use Concrete\Core\Permission\Key\BlockKey as BlockPermissionKey;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;

class PageResponse extends Response
{
    // legacy support
    public function canWrite()
    {
        return $this->validate('edit_page_contents');
    }

    public function canReadVersions()
    {
        return $this->validate('view_page_versions');
    }

    public function canRead()
    {
        return $this->canViewPage();
    }

    public function canAddSubContent()
    {
        return $this->validate('add_subpage');
    }

    public function canViewPageInSitemap()
    {
        $app = Application::getFacadeApplication();
        /** @var Repository $config */
        $config = $app->make(Repository::class);
        if ($config->get('concrete.permissions.model') != 'simple') {
            $pk = $this->category->getPermissionKeyByHandle('view_page_in_sitemap');
            $pk->setPermissionObject($this->object);

            return $pk->validate();
        }

        return $this->canViewPage();
    }

    public function canViewPage()
    {
        return $this->validate('view_page');
    }

    public function canAddSubpages()
    {
        return $this->validate('add_subpage');
    }

    public function canDeleteCollection()
    {
        return $this->canDeletePage();
    }

    public function canEditPageType()
    {
        return $this->validate('edit_page_page_type');
    }

    public function canApproveCollection()
    {
        return $this->validate('approve_page_versions');
    }

    public function canAdminPage()
    {
        return $this->validate('edit_page_permissions');
    }

    public function canAdmin()
    {
        return $this->validate('edit_page_permissions');
    }

    public function canAddExternalLink()
    {
        $pk = $this->category->getPermissionKeyByHandle('add_subpage');
        $pk->setPermissionObject($this->object);

        return $pk->canAddExternalLink();
    }

    public function canAddSubCollection($ct)
    {
        $pk = $this->category->getPermissionKeyByHandle('add_subpage');
        $pk->setPermissionObject($this->object);

        return $pk->validate($ct);
    }

    public function canAddBlockType($bt)
    {
        // Check can add the block to any area on the site.
        $key = Key::getByHandle('add_block');
        if (!$key || !$key->validate($bt)) {
            return false;
        }

        // Check can add blocks to this area.
        $list = Area::getListOnPage($this->object);
        foreach ($list as $la) {
            $lap = new Permissions($la);
            if ($lap->canAddBlockToArea($bt)) {
                return true;
            }
        }

        return false;
    }

    public function canEditPageProperties($obj = false)
    {
        $pk = $this->category->getPermissionKeyByHandle('edit_page_properties');
        $pk->setPermissionObject($this->object);

        return $pk->validate($obj);
    }

    public function canDeletePage()
    {
        return $this->validate('delete_page');
    }

    // end legacy

    // convenience function
    public function canViewToolbar()
    {
        $app = Application::getFacadeApplication();
        /** @var RequestCache $cache */
        $cache = $app->make('cache/request');
        $identifier = 'permission/response/canviewtoolbar';
        $cacheItem = $cache->getItem($identifier);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $u = $app->make(User::class);
        if (!$u->isRegistered()) {
            $cache->save($cacheItem->set(false));
            return false;
        }
        if ($u->isSuperUser()) {
            $cache->save($cacheItem->set(true));
            return true;
        }

        $sh = $app->make('helper/concrete/dashboard/sitemap');
        if ($sh->canViewSitemapPanel()) {
            $cache->save($cacheItem->set(true));
            return true;
        }

        $dh = $app->make('helper/concrete/dashboard');
        if ($dh->canRead() ||
            $this->canViewPageVersions() ||
            $this->canPreviewPageAsUser() ||
            $this->canEditPageSpeedSettings() ||
            $this->canEditPageProperties() ||
            $this->canEditPageContents() ||
            $this->canAddSubpage() ||
            $this->canDeletePage() ||
            $this->canApprovePageVersions() ||
            $this->canEditPagePermissions() ||
            $this->canMoveOrCopyPage()
        ) {
            $cache->save($cacheItem->set(true));
            return true;
        }
        $c = Page::getCurrentPage();
        if ($c && $c->getCollectionPath() == STACKS_LISTING_PAGE_PATH) {
            $cache->save($cacheItem->set(true));
            return true;
        }

        $cache->save($cacheItem->set(false));
        return false;
    }

    public function testForErrors()
    {
        if ($this->object->isMasterCollection()) {
            $canEditMaster = Key::getByHandle('access_page_defaults')->validate();
            /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
            $session = Application::getFacadeApplication()->make('session');
            if (!($canEditMaster && $session->get('mcEditID') == $this->object->getCollectionID())) {
                return COLLECTION_FORBIDDEN;
            }
        } else {
            if ((!$this->canViewPage()) && (!$this->object->getCollectionPointerExternalLink() != '')) {
                return COLLECTION_FORBIDDEN;
            }
        }

        return parent::testForErrors();
    }

    /**
     * @deprecated Never used since 5.7.0.
     */
    public function getAllTimedAssignmentsForPage()
    {
        return $this->getAllAssignmentsForPage();
    }

    /**
     * @deprecated Never used since 5.7.0.
     */
    public function getAllAssignmentsForPage()
    {
        $db = Loader::db();
        $assignments = [];
        $r = $db->Execute(
            'select peID, pkID, pdID from PagePermissionAssignments ppa inner join PermissionAccessList pal on ppa.paID = pal.paID where cID = ?',
            [$this->object->getCollectionID()]
        );
        while ($row = $r->fetch()) {
            $pk = PagePermissionKey::getByID($row['pkID']);
            $pae = PermissionAccessEntity::getByID($row['peID']);
            $pd = PermissionDuration::getByID($row['pdID']);
            $ppc = new PageContentPermissionTimedAssignment();
            $ppc->setDurationObject($pd);
            $ppc->setAccessEntityObject($pae);
            $ppc->setPermissionKeyObject($pk);
            $assignments[] = $ppc;
        }
        $r = $db->Execute(
            'select arHandle from Areas where cID = ? and arOverrideCollectionPermissions = 1',
            [$this->object->getCollectionID()]
        );
        while ($row = $r->fetch()) {
            $r2 = $db->Execute(
                'select peID, pdID, pkID from AreaPermissionAssignments apa inner join PermissionAccessList pal on apa.paID = pal.paID where cID = ? and arHandle = ?',
                [$this->object->getCollectionID(), $row['arHandle']]
            );
            while ($row2 = $r2->fetch()) {
                $pk = AreaPermissionKey::getByID($row2['pkID']);
                $pae = PermissionAccessEntity::getByID($row2['peID']);
                $area = Area::get($this->getPermissionObject(), $row['arHandle']);
                $pk->setPermissionObject($area);
                $pd = PermissionDuration::getByID($row2['pdID']);
                $ppc = new PageContentPermissionTimedAssignment();
                $ppc->setDurationObject($pd);
                $ppc->setAccessEntityObject($pae);
                $ppc->setPermissionKeyObject($pk);
                $assignments[] = $ppc;
            }
        }
        $r = $db->Execute(
            'select peID, cvb.cvID, cvb.bID, pdID, pkID from BlockPermissionAssignments bpa
                    inner join PermissionAccessList pal on bpa.paID = pal.paID inner join CollectionVersionBlocks cvb on cvb.cID = bpa.cID and cvb.cvID = bpa.cvID and cvb.bID = bpa.bID
                    where cvb.cID = ? and cvb.cvID = ? and cvb.cbOverrideAreaPermissions = 1',
            [$this->object->getCollectionID(), $this->object->getVersionID()]
        );
        while ($row = $r->fetch()) {
            $pk = BlockPermissionKey::getByID($row['pkID']);
            $pae = PermissionAccessEntity::getByID($row['peID']);
            $arHandle = $db->GetOne(
                'select arHandle from CollectionVersionBlocks where bID = ? and cvID = ? and cID = ?',
                [
                    $row['bID'],
                    $row['cvID'],
                    $this->object->getCollectionID(),
                ]
            );
            $b = Block::getByID($row['bID'], $this->object, $arHandle);
            $pk->setPermissionObject($b);
            $pd = PermissionDuration::getByID($row['pdID']);
            $ppc = new PageContentPermissionTimedAssignment();
            $ppc->setDurationObject($pd);
            $ppc->setAccessEntityObject($pae);
            $ppc->setPermissionKeyObject($pk);
            $assignments[] = $ppc;
        }

        return $assignments;
    }
}
