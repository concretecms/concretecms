<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Foundation\ConcreteObject;
use CacheLocal;
use Concrete\Core\Permission\Access\ListItem\ListItem;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use User;
use Core;
use Database;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Workflow\Workflow;

class Access extends ConcreteObject
{
    protected $paID;
    protected $paIDList = array();
    protected $listItems;

    public function setListItems($listItems)
    {
        $this->listItems = $listItems;
    }

    public function setPermissionKey($permissionKey)
    {
        $this->pk = $permissionKey;
    }

    public function getPermissionObject()
    {
        return $this->pk->getPermissionObject();
    }

    public function getPermissionObjectToCheck()
    {
        return $this->pk->getPermissionObjectToCheck();
    }

    public function getPermissionAccessID()
    {
        return $this->paID;
    }

    public function isPermissionAccessInUse()
    {
        return $this->paIsInUse;
    }

    public function getPermissionAccessIDList()
    {
        return $this->paIDList;
    }

    protected function deliverAccessListItems($q, $accessType, $filterEntities)
    {
        $db = Database::connection();
        $class = '\\Concrete\\Core\\Permission\\Access\\ListItem\\' . Core::make('helper/text')->camelcase(
                $this->pk->getPermissionKeyCategoryHandle()
            ) . 'ListItem';
        if ($this->pk->permissionKeyHasCustomClass()) {
            $class = '\\Concrete\\Core\\Permission\\Access\\ListItem\\' . Core::make('helper/text')->camelcase(
                    $this->pk->getPermissionKeyHandle() . '_' . $this->pk->getPermissionKeyCategoryHandle()
                ) . 'ListItem';
        }

        if (!class_exists($class)) {
            $class = '\\Concrete\\Core\\Permission\\Access\\ListItem\\ListItem';
        }

        // Now that we have the proper list item class, let's see if we have a custom list item array we've passed
        // in from the contents of another permission key. If we do, we loop through those, setting their relevant
        // parameters on our new access list item

        $list = array();
        if (isset($this->listItems)) {
            foreach($this->listItems as $listItem) {
                $addToList = false;
                if (count($filterEntities) > 0) {
                    foreach($filterEntities as $filterEntity) {
                        if ($filterEntity->getAccessEntityID() == $listItem->getAccessEntityObject()->getAccessEntityID()) {
                            $addToList = true;
                        }
                    }
                } else {
                    $addToList = true;
                }

                if ($addToList) {
                    /**
                     * @var $listItem ListItem
                     * @var $obj ListItem
                     */
                    $obj = Core::make($class);
                    $obj->setAccessType($listItem->getAccessType());
                    $obj->setPermissionAccessID($listItem->getPermissionAccessID());
                    $obj->setAccessEntityObject($listItem->getAccessEntityObject());
                    $obj->setPermissionDurationObject($listItem->getPermissionDurationObject());
                    $list[] = $obj;
                }
            }

        } else {

            $filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
            $q = $q . ' ' . $filterString;
            $r = $db->executeQuery($q);
            while ($row = $r->FetchRow()) {
                $obj = Core::make($class);
                $obj->setPropertiesFromArray($row);
                if ($row['pdID']) {
                    $obj->loadPermissionDurationObject($row['pdID']);
                }
                if ($row['peID']) {
                    $obj->loadAccessEntityObject($row['peID']);
                }
                $list[] = $obj;
            }

        }

        return $list;
    }

    public function validateAndFilterAccessEntities($accessEntities)
    {
        $entities = array();
        foreach ($accessEntities as $ae) {
            if ($ae->validate($this)) {
                $entities[] = $ae;
            }
        }

        return $entities;
    }

    public function validateAccessEntities($accessEntities)
    {
        $valid = false;
        $accessEntities = $this->validateAndFilterAccessEntities($accessEntities);
        $list = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
        $list = PermissionDuration::filterByActive($list);
        foreach ($list as $l) {
            if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
                $valid = true;
            }
            if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
                $valid = false;
            }
        }

        return $valid;
    }

    public function validate()
    {
        $u = new User();
        if ($u->isSuperUser()) {
            return true;
        }
        $accessEntities = $u->getUserAccessEntityObjects();

        return $this->validateAccessEntities($accessEntities);
    }

    public static function createByMerge($permissions)
    {
        $class = get_class($permissions[0]);
        $p = new $class();
        foreach ($permissions as $px) {
            $p->paIDList[] = $px->getPermissionAccessID();
        }
        $p->pk = $permissions[0]->pk;
        $p->paID = -1;

        return $p;
    }

    protected function getCacheIdentifier($accessType, $filterEntities = array())
    {
        $filter = $accessType . ':';
        foreach ($filterEntities as $pae) {
            $filter .= $pae->getAccessEntityID() . ':';
        }
        $filter = trim($filter, ':');
        $paID = $this->getPermissionAccessID();
        $class = strtolower(get_class($this->pk));
        return sprintf('permission/access/list_items/%s/%s/%s', $paID, $filter, $class);
    }

    public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array(), $checkCache = true)
    {
        if (count($this->paIDList) > 0) {
            $q = 'select paID, peID, pdID, accessType from PermissionAccessList where paID in (' . implode(
                    ',',
                    $this->paIDList
                ) . ')';

            return $this->deliverAccessListItems($q, $accessType, $filterEntities);
        } else {
            // Sometimes we want to disable cache checking here because we're going to be
            // adding items to the cache from a class that subclasses this one. See
            // AddBlockToAreaAreaAccess

            if ($checkCache) {
                $cache = \Core::make('cache/request');
                $item = $cache->getItem($this->getCacheIdentifier($accessType, $filterEntities));
                if (!$item->isMiss()) {
                    return $item->get();
                }
                $item->lock();
            }

            $q = 'select paID, peID, pdID, accessType from PermissionAccessList where paID = ' . $this->getPermissionAccessID(
                );
            $items = $this->deliverAccessListItems($q, $accessType, $filterEntities);

            if ($checkCache) {
                $cache->save($item->set($items));
            }

            return $items;
        }
    }

    protected function buildAssignmentFilterString($accessType, $filterEntities)
    {
        $peIDs = '';
        $filters = array();
        if (count($filterEntities) > 0) {
            foreach ($filterEntities as $ent) {
                $filters[] = $ent->getAccessEntityID();
            }
            $peIDs .= 'and peID in (' . implode($filters, ',') . ')';
        }
        if ($accessType == 0) {
            $accessType = '';
        } else {
            $connection = \Database::connection();
            $accessType = $connection->quote($accessType, \PDO::PARAM_INT);
            $accessType = ' and accessType = ' . $accessType;
        }

        return $peIDs . ' ' . $accessType . ' order by accessType desc'; // we order desc so that excludes come last (-1)
    }

    public function clearWorkflows()
    {
        $db = Database::connection();
        $db->executeQuery('delete from PermissionAccessWorkflows where paID = ?', array($this->getPermissionAccessID()));
    }

    public function attachWorkflow(Workflow $wf)
    {
        $db = Database::connection();
        $db->Replace(
            'PermissionAccessWorkflows',
            array('paID' => $this->getPermissionAccessID(), 'wfID' => $wf->getWorkflowID()),
            array('paID', 'wfID'),
            true
        );
    }

    public function removeWorkflow(Workflow $wf)
    {
        $db = Database::connection();
        $db->executeQuery('delete from PermissionAccessWorkflows where paID = ? and wfID = ?', array(
            $this->getPermissionAccessID(), $wf->getWorkflowID()
        ));
    }

    public function getWorkflows()
    {
        $db = Database::connection();
        $r = $db->executeQuery(
            'select wfID from PermissionAccessWorkflows where paID = ?',
            array($this->getPermissionAccessID())
        );
        $workflows = array();
        while ($row = $r->FetchRow()) {
            $wf = Workflow::getByID($row['wfID']);
            if (is_object($wf)) {
                $workflows[] = $wf;
            }
        }

        return $workflows;
    }

    public function duplicate($newPA = false)
    {
        $db = Database::connection();
        if (!$newPA) {
            $newPA = self::create($this->pk);
        }
        $listItems = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL);
        foreach ($listItems as $li) {
            $newPA->addListItem($li->getAccessEntityObject(), $li->getPermissionDurationObject(), $li->getAccessType());
        }
        $workflows = $this->getWorkflows();
        foreach ($workflows as $wf) {
            $newPA->attachWorkflow($wf);
        }
        $newPA->setPermissionKey($this->pk);

        return $newPA;
    }

    public function markAsInUse()
    {
        $db = Database::connection();
        $db->executeQuery('update PermissionAccess set paIsInUse = 1 where paID = ?', array($this->paID));
    }

    public function addListItem(
        PermissionAccessEntity $pae,
        $durationObject = false,
        $accessType = PermissionKey::ACCESS_TYPE_INCLUDE
    ) {
        $db = Database::connection();
        $pdID = 0;
        if ($durationObject instanceof PermissionDuration) {
            $pdID = $durationObject->getPermissionDurationID();
        }

        $db->Replace(
            'PermissionAccessList',
            array(
                'paID' => $this->getPermissionAccessID(),
                'peID' => $pae->getAccessEntityID(),
                'pdID' => $pdID,
                'accessType' => $accessType,
            ),
            array('paID', 'peID'),
            false
        );
    }

    public function removeListItem(PermissionAccessEntity $pe)
    {
        $db = Database::connection();
        $db->executeQuery(
            'delete from PermissionAccessList where peID = ? and paID = ?',
            array($pe->getAccessEntityID(), $this->getPermissionAccessID())
        );
    }

    public function save($args = array())
    {
    }

    public static function create(PermissionKey $pk)
    {
        $db = Database::connection();
        $db->executeQuery('insert into PermissionAccess (paIsInUse) values (0)');

        return static::getByID($db->lastInsertId(), $pk);
    }

    public static function getByID($paID, PermissionKey $pk, $checkPA = true)
    {
        $cache = Core::make('cache/request');
        $identifier = sprintf('permission/access/%s/%s', $pk->getPermissionKeyID(), $paID);
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }

        $db = Database::connection();

        $handle = $pk->getPermissionKeyCategoryHandle();
        if ($pk->permissionKeyHasCustomClass()) {
            $handle = $pk->getPermissionKeyHandle() . '_' . $handle;
        }

        $class = '\\Core\\Permission\\Access\\' . Core::make('helper/text')->camelcase($handle) . 'Access';
        $class = core_class($class, $pk->getPackageHandle());

        $obj = null;
        if ($checkPA) {
            $row = $db->GetRow('select paID, paIsInUse from PermissionAccess where paID = ?', array($paID));
            if ($row && $row['paID']) {
                $obj = Core::make($class);
                $obj->setPropertiesFromArray($row);
            }
        } else { // we got here from an assignment object so we already know its in use.
            $obj = Core::make($class);
            $obj->paID = $paID;
            $obj->paIsInUse = true;
        }
        if (isset($obj)) {
            $obj->setPermissionKey($pk);
        }

        $item->set($obj);

        return $obj;
    }
}
