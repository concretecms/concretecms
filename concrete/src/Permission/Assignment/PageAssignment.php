<?php

namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Application;

/**
 * @property \Concrete\Core\Permission\Key\PageKey $pk
 *
 * @method \Concrete\Core\Page\Page getPermissionObject()
 */
class PageAssignment extends Assignment
{
    /**
     * Permissions that come from "Edit Page Type Draft" permissions.
     *
     * @var array
     */
    protected $inheritedPageTypeDraftPermissions = [
        'view_page' => 'edit_page_type_drafts',
        'view_page_versions' => 'edit_page_type_drafts',
        'view_page_in_sitemap' => 'edit_page_type_drafts',
        'edit_page_contents' => 'edit_page_type_drafts',
        'edit_page_properties' => 'edit_page_type_drafts',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Assignment\Assignment::getPermissionAccessObject()
     *
     * @return \Concrete\Core\Permission\Access\PageAccess|null
     */
    public function getPermissionAccessObject()
    {
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        $identifier = sprintf(
            'permission/assignment/access/%s/%s',
            $this->pk->getPermissionKeyHandle(),
            $this->getPermissionObject()->getPermissionObjectIdentifier()
        );
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }
        $db = $app->make(Connection::class);
        $r = $db->fetchColumn('select paID from PagePermissionAssignments where cID = ? and pkID = ?', [$this->getPermissionObject()->getPermissionsCollectionID(), $this->pk->getPermissionKeyID()]);
        $pa = $r ? Access::getByID($r, $this->pk, false) : null;
        if ($pa) {
            $permissionObject = $this->getPermissionObject();
            if ($permissionObject->isPageDraft() && $permissionObject->getCollectionInheritance() == 'PARENT' && isset($this->inheritedPageTypeDraftPermissions[$this->pk->getPermissionKeyHandle()])) {
                $pageType = $permissionObject->getPageTypeObject();
                if (is_object($pageType)) {
                    $pk = Key::getByHandle($this->inheritedPageTypeDraftPermissions[$this->pk->getPermissionKeyHandle()]);
                    $pk->setPermissionObject($pageType);
                    $access = $pk->getPermissionAccessObject();
                    if (is_object($access)) {
                        $list_items = $access->getAccessListItems();
                        $pa->setListItems($list_items);
                    }
                }
            }
        }
        $cache->save($item->set($pa));

        return $pa;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Assignment\Assignment::clearPermissionAssignment()
     */
    public function clearPermissionAssignment()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update PagePermissionAssignments set paID = 0 where pkID = ? and cID = ?', [$this->pk->getPermissionKeyID(), $this->getPermissionObject()->getPermissionsCollectionID()]);

        $cache = $app->make('cache/request');
        $identifier = sprintf(
            'permission/assignment/access/%s/%s',
            $this->pk->getPermissionKeyHandle(),
            $this->getPermissionObject()->getPermissionObjectIdentifier()
        );
        $cache->delete($identifier);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Assignment\Assignment::assignPermissionAccess()
     */
    public function assignPermissionAccess(Access $pa)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->replace(
            'PagePermissionAssignments',
            ['cID' => $this->getPermissionObject()->getPermissionsCollectionID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()],
            ['cID', 'pkID'],
            true
        );
        $pa->markAsInUse();

        $cache = $app->make('cache/request');
        $identifier = sprintf(
            'permission/assignment/access/%s/%s',
            $this->pk->getPermissionKeyHandle(),
            $this->getPermissionObject()->getPermissionObjectIdentifier()
        );
        $cache->delete($identifier);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Assignment\Assignment::getPermissionKeyTaskURL()
     */
    public function getPermissionKeyTaskURL(string $task = '', array $options = []): string
    {
        $pageArray = $this->pk->getMultiplePageArray();
        if (is_array($pageArray) && $pageArray !== []) {
            $cIDs = [];
            foreach ($pageArray as $sc) {
                $cIDs[] = $sc->getCollectionID();
            }
            $options += ['cID' => $cIDs];
        } else {
            $options += ['cID' => $this->getPermissionObject()->getCollectionID()];
        }

        return parent::getPermissionKeyTaskURL($task, $options);
    }
}
