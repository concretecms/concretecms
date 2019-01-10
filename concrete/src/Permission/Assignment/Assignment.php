<?php

namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Support\Facade\Application;
use PermissionKeyCategory;

class Assignment
{
    /**
     * @var \Concrete\Core\Permission\Key\Key|null
     */
    protected $pk;

    /**
     * The object of the permission (for example, a Page instance).
     *
     * @var \Concrete\Core\Permission\ObjectInterface|null
     */
    protected $permissionObject;

    /**
     * Set the object of the permission (for example, a Page instance).
     *
     * @param \Concrete\Core\Permission\ObjectInterface $po
     */
    public function setPermissionObject($po)
    {
        $this->permissionObject = $po;
    }

    /**
     * Get the object of the permission (for example, a Page instance).
     *
     * @return \Concrete\Core\Permission\ObjectInterface|null
     */
    public function getPermissionObject()
    {
        return $this->permissionObject;
    }

    /**
     * @param \Concrete\Core\Permission\Key\Key $pk
     */
    public function setPermissionKeyObject($pk)
    {
        $this->pk = $pk;
    }

    /**
     * @param string|null|false $task
     *
     * @return string
     */
    public function getPermissionKeyToolsURL($task = false)
    {
        $app = Application::getFacadeApplication();
        if (!$task) {
            $task = 'save_permission';
        }
        $uh = $app->make('helper/concrete/urls');
        $class = substr(get_class($this), 0, strrpos(get_class($this), 'PermissionAssignment'));
        $handle = $app->make('helper/text')->uncamelcase($class);
        if ($handle) {
            $akc = PermissionKeyCategory::getByHandle($handle);
        } else {
            $akc = PermissionKeyCategory::getByID($this->pk->getPermissionKeyCategoryID());
        }
        $url = $uh->getToolsURL('permissions/categories/' . $akc->getPermissionKeyCategoryHandle(), $akc->getPackageHandle());
        $token = $app->make('helper/validation/token')->getParameter($task);
        $url .= '?' . $token . '&task=' . $task . '&pkID=' . $this->pk->getPermissionKeyID();

        return $url;
    }

    public function clearPermissionAssignment()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update PermissionAssignments set paID = 0 where pkID = ?', [$this->pk->getPermissionKeyID()]);
    }

    /**
     * @param \Concrete\Core\Permission\Access\Access $pa
     */
    public function assignPermissionAccess(Access $pa)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->replace(
            'PermissionAssignments',
            ['paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()],
            ['pkID'],
            true
        );
        $pa->markAsInUse();
    }

    /**
     * @return \Concrete\Core\Permission\Access\Access|null
     */
    public function getPermissionAccessObject()
    {
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        $identifier = sprintf('permission/key/assignment/%s', $this->pk->getPermissionKeyID());
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }
        $item->lock();
        $db = $app->make(Connection::class);
        $paID = $db->fetchColumn('select paID from PermissionAssignments where pkID = ?', [$this->pk->getPermissionKeyID()]);
        $pa = $paID ? Access::getByID($paID, $this->pk) : null;
        $cache->save($item->set($pa));

        return $pa;
    }
}
