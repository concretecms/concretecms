<?php

namespace Concrete\Core\Permission\Access;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Logging\Entry\Permission\Assignment\Assignment as PermissionAssignmentLogEntry;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Permission\Logger;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Workflow;
use PDO;

/**
 * @property \Concrete\Core\Permission\Key\Key $pk
 * @property bool|int|string $paIsInUse
 */
class Access extends ConcreteObject
{
    /**
     * @var int
     */
    protected $paID;

    /**
     * @var int[]
     */
    protected $paIDList = [];

    /**
     * @var \Concrete\Core\Permission\Access\ListItem\ListItem[]|null
     */
    protected $listItems;

    /**
     * @param \Concrete\Core\Permission\Access\ListItem\ListItem[] $listItems
     */
    public function setListItems($listItems)
    {
        $this->listItems = $listItems;
    }

    /**
     * @param \Concrete\Core\Permission\Key\Key $permissionKey
     */
    public function setPermissionKey($permissionKey)
    {
        $this->pk = $permissionKey;
    }

    /**
     * Get the object associated to the permission (for example, a Page instance).
     *
     * @return object
     */
    public function getPermissionObject()
    {
        return $this->pk->getPermissionObject();
    }

    /**
     * Get the object to be used to check the permission (for example, a Page instance).
     *
     * @return object
     */
    public function getPermissionObjectToCheck()
    {
        return $this->pk->getPermissionObjectToCheck();
    }

    /**
     * @return int
     */
    public function getPermissionAccessID()
    {
        return $this->paID;
    }

    /**
     * @return bool|int|string
     */
    public function isPermissionAccessInUse()
    {
        return $this->paIsInUse;
    }

    /**
     * @return int[]
     */
    public function getPermissionAccessIDList()
    {
        return $this->paIDList;
    }

    /**
     * @param \Concrete\Core\Permission\Access\Entity\Entity[] $accessEntities
     *
     * @return \Concrete\Core\Permission\Access\Entity\Entity[]
     */
    public function validateAndFilterAccessEntities($accessEntities)
    {
        $entities = [];
        foreach ($accessEntities as $ae) {
            if ($ae->validate($this)) {
                $entities[] = $ae;
            }
        }

        return $entities;
    }

    /**
     * @param \Concrete\Core\Permission\Access\Entity\Entity[] $accessEntities
     *
     * @return bool
     */
    public function validateAccessEntities($accessEntities)
    {
        $valid = false;
        $accessEntities = $this->validateAndFilterAccessEntities($accessEntities);
        $list = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
        $list = PermissionDuration::filterByActive($list);
        foreach ($list as $l) {
            switch ($l->getAccessType()) {
                case PermissionKey::ACCESS_TYPE_INCLUDE:
                    $valid = true;
                    break;
                case PermissionKey::ACCESS_TYPE_EXCLUDE:
                    $valid = false;
                    break;
            }
        }

        return $valid;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);
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

    /**
     * @param int $accessType
     * @param \Concrete\Core\Permission\Access\Entity\Entity[] $filterEntities
     * @param bool $checkCache
     *
     * @return \Concrete\Core\Permission\Access\ListItem\ListItem[]
     */
    public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = [], $checkCache = true)
    {
        if (count($this->paIDList) > 0) {
            $q = 'select paID, peID, pdID, accessType from PermissionAccessList where paID in (' . implode(',', $this->paIDList) . ')';

            return $this->deliverAccessListItems($q, $accessType, $filterEntities);
        }
        // Sometimes we want to disable cache checking here because we're going to be
        // adding items to the cache from a class that subclasses this one. See
        // AddBlockToAreaAreaAccess
        if ($checkCache) {
            $app = Application::getFacadeApplication();
            $cache = $app->make('cache/request');
            $item = $cache->getItem($this->getCacheIdentifier($accessType, $filterEntities));
            if (!$item->isMiss()) {
                return $item->get();
            }
            $item->lock();
        }

        $q = 'select paID, peID, pdID, accessType from PermissionAccessList where paID = ' . $this->getPermissionAccessID();
        $items = $this->deliverAccessListItems($q, $accessType, $filterEntities);

        if ($checkCache) {
            $cache->save($item->set($items));
        }

        return $items;
    }

    public function clearWorkflows()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('delete from PermissionAccessWorkflows where paID = ?', [$this->getPermissionAccessID()]);
    }

    /**
     * @param \Concrete\Core\Workflow\Workflow $wf
     */
    public function attachWorkflow(Workflow $wf)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->replace(
            'PermissionAccessWorkflows',
            ['paID' => $this->getPermissionAccessID(), 'wfID' => $wf->getWorkflowID()],
            ['paID', 'wfID'],
            true
        );
    }

    /**
     * @param \Concrete\Core\Workflow\Workflow $wf
     */
    public function removeWorkflow(Workflow $wf)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery(
            'delete from PermissionAccessWorkflows where paID = ? and wfID = ?',
            [$this->getPermissionAccessID(), $wf->getWorkflowID()]
        );
    }

    /**
     * @return \Concrete\Core\Workflow\Workflow[]
     */
    public function getWorkflows()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $r = $db->executeQuery(
            'select wfID from PermissionAccessWorkflows where paID = ?',
            [$this->getPermissionAccessID()]
        );
        $workflows = [];
        while (($wfID = $r->fetchColumn()) !== false) {
            $wf = Workflow::getByID($wfID);
            if ($wf) {
                $workflows[] = $wf;
            }
        }

        return $workflows;
    }

    /**
     * @param \Concrete\Core\Permission\Access\Access|null|false $newPA
     *
     * @return \Concrete\Core\Permission\Access\Access
     */
    public function duplicate($newPA = false)
    {
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update PermissionAccess set paIsInUse = 1 where paID = ?', [$this->paID]);
        $this->paIsInUse = true;

        $logger = $app->make(Logger::class);
        $entry = $app->make(PermissionAssignmentLogEntry::class, [
           'applier' => $app->make(User::class),
           'key' => $this->pk,
           'access' => $this,
        ]);
        $logger->log($entry);
    }

    /**
     * @param \Concrete\Core\Permission\Access\Entity\Entity $pae
     * @param \Concrete\Core\Permission\Duration|null|false $durationObject
     * @param int $accessType
     */
    public function addListItem(PermissionAccessEntity $pae, $durationObject = false, $accessType = PermissionKey::ACCESS_TYPE_INCLUDE)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        if ($durationObject instanceof PermissionDuration) {
            $pdID = $durationObject->getPermissionDurationID();
        } else {
            $pdID = 0;
        }

        $db->replace(
            'PermissionAccessList',
            [
                'paID' => $this->getPermissionAccessID(),
                'peID' => $pae->getAccessEntityID(),
                'pdID' => $pdID,
                'accessType' => $accessType,
            ],
            ['paID', 'peID'],
            false
        );
    }

    /**
     * @param \Concrete\Core\Permission\Access\Entity\Entity $pe
     */
    public function removeListItem(PermissionAccessEntity $pe)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery(
            'delete from PermissionAccessList where peID = ? and paID = ?',
            [$pe->getAccessEntityID(), $this->getPermissionAccessID()]
        );
    }

    /**
     * @param array $args
     */
    public function save($args = [])
    {
    }

    /**
     * @param \Concrete\Core\Permission\Key\Key $pk
     *
     * @return \Concrete\Core\Permission\Access\Access
     */
    public static function create(PermissionKey $pk)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('insert into PermissionAccess (paIsInUse) values (0)');

        return static::getByID($db->lastInsertId(), $pk);
    }

    /**
     * @param int $paID
     * @param \Concrete\Core\Permission\Key\Key $pk
     * @param bool $checkPA
     *
     * @return \Concrete\Core\Permission\Access\Access|null
     */
    public static function getByID($paID, PermissionKey $pk, $checkPA = true)
    {
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        $identifier = sprintf('permission/access/%s/%s', $pk->getPermissionKeyID(), $paID);
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }

        $db = $app->make(Connection::class);

        $handle = $pk->getPermissionKeyCategoryHandle();
        if ($pk->permissionKeyHasCustomClass()) {
            $handle = $pk->getPermissionKeyHandle() . '_' . $handle;
        }

        $class = '\\Core\\Permission\\Access\\' . $app->make('helper/text')->camelcase($handle) . 'Access';
        $class = core_class($class, $pk->getPackageHandle());

        $obj = null;
        if ($checkPA) {
            if ($paID) {
                $row = $db->fetchAssoc('select paID, paIsInUse from PermissionAccess where paID = ?', [$paID]);
                if ($row) {
                    $obj = $app->make($class);
                    $obj->setPropertiesFromArray($row);
                }
            }
        } else { // we got here from an assignment object so we already know its in use.
            $obj = $app->make($class);
            $obj->paID = $paID;
            $obj->paIsInUse = true;
        }
        if ($obj !== null) {
            $obj->setPermissionKey($pk);
        }

        $item->set($obj);

        return $obj;
    }

    /**
     * @param string $q
     * @param int $accessType
     * @param \Concrete\Core\Permission\Access\Entity\Entity[] $filterEntities
     *
     * @return \Concrete\Core\Permission\Access\ListItem\ListItem[]
     */
    protected function deliverAccessListItems($q, $accessType, $filterEntities)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        if ($this->pk->permissionKeyHasCustomClass()) {
            $class = '\\Concrete\\Core\\Permission\\Access\\ListItem\\' . $app->make('helper/text')->camelcase($this->pk->getPermissionKeyHandle() . '_' . $this->pk->getPermissionKeyCategoryHandle()) . 'ListItem';
        } else {
            $class = '\\Concrete\\Core\\Permission\\Access\\ListItem\\' . $app->make('helper/text')->camelcase($this->pk->getPermissionKeyCategoryHandle()) . 'ListItem';
        }
        if (!class_exists($class)) {
            $class = '\\Concrete\\Core\\Permission\\Access\\ListItem\\ListItem';
        }
        // Now that we have the proper list item class, let's see if we have a custom list item array we've passed
        // in from the contents of another permission key. If we do, we loop through those, setting their relevant
        // parameters on our new access list item
        $list = [];
        if ($this->listItems !== null) {
            foreach ($this->listItems as $listItem) {
                $addToList = false;
                if (count($filterEntities) > 0) {
                    foreach ($filterEntities as $filterEntity) {
                        if ($filterEntity->getAccessEntityID() == $listItem->getAccessEntityObject()->getAccessEntityID()) {
                            $addToList = true;
                            break;
                        }
                    }
                } else {
                    $addToList = true;
                }
                if ($addToList) {
                    $obj = $app->make($class);
                    /* @var \Concrete\Core\Permission\Access\ListItem\ListItem $obj */
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
            while (($row = $r->fetch(PDO::FETCH_ASSOC)) !== false) {
                $obj = $app->build($class);
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

    /**
     * @param int $accessType
     * @param \Concrete\Core\Permission\Access\Entity\Entity[] $filterEntities
     *
     * @return string
     */
    protected function getCacheIdentifier($accessType, $filterEntities = [])
    {
        $filter = $accessType . ':';
        foreach ($filterEntities as $pae) {
            $filter .= $pae->getAccessEntityID() . ':';
        }
        $filter = trim($filter, ':');
        if ($this->listItems !== null) {
            $aeIDs = [];
            foreach ($this->listItems as $listItem) {
                $aeIDs[] = $listItem->getPermissionAccessID();
            }
            $paID = implode(':', $aeIDs);
        } else {
            $paID = $this->getPermissionAccessID();
        }
        $class = strtolower(get_class($this->pk));

        return sprintf('permission/access/list_items/%s/%s/%s', $paID, $filter, $class);
    }

    /**
     * @param int $accessType
     * @param \Concrete\Core\Permission\Access\Entity\Entity[] $filterEntities
     *
     * @return string
     */
    protected function buildAssignmentFilterString($accessType, $filterEntities)
    {
        $peIDs = '';
        $filters = [];
        if (count($filterEntities) > 0) {
            foreach ($filterEntities as $ent) {
                $filters[] = $ent->getAccessEntityID();
            }
            $peIDs .= 'and peID in (' . implode(',', $filters) . ')';
        }
        if ($accessType == 0) {
            $accessType = '';
        } else {
            $app = Application::getFacadeApplication();
            $connection = $app->make(Connection::class);
            $accessType = $connection->quote($accessType, PDO::PARAM_INT);
            $accessType = ' and accessType = ' . $accessType;
        }

        return $peIDs . ' ' . $accessType . ' order by accessType desc'; // we order desc so that excludes come last (-1)
    }
}
