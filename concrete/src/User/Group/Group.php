<?php
namespace Concrete\Core\User\Group;

use CacheLocal;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Config;
use Database;
use Events;
use File;
use Gettext\Translations;
use GroupTree;
use GroupTreeNode;
use Concrete\Core\User\UserList;

class Group extends ConcreteObject implements \Concrete\Core\Permission\ObjectInterface
{
    public $ctID;
    public $permissionSet;
    private $permissions = []; // more advanced version of permissions

    public function getPermissionObjectIdentifier()
    {
        return $this->gID;
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\GroupResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return false;
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return false;
    }

    /**
     * Takes the numeric id of a group and returns a group object.
     *
     * @param string $gID
     *
     * @return Group
     */
    public static function getByID($gID)
    {
        $db = Database::connection();
        $g = CacheLocal::getEntry('group', $gID);
        if (is_object($g)) {
            return $g;
        }

        $row = $db->fetchAssoc('select * from ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' where gID = ?', [$gID]);
        if ($row) {
            $g = \Core::make('\Concrete\Core\User\Group\Group');
            $g->setPropertiesFromArray($row);
            CacheLocal::set('group', $gID, $g);

            return $g;
        }
    }

    /**
     * Takes the name of a group and returns a group object.
     *
     * @param string $gName
     *
     * @return Group
     */
    public static function getByName($gName)
    {
        $db = Database::connection();
        $row = $db->fetchAssoc('select * from ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' where gName = ?', [$gName]);
        if ($row) {
            $g = new static();
            $g->setPropertiesFromArray($row);

            return $g;
        }
    }

    /**
     * @param string $gPath The group path
     *
     * @return Group
     */
    public static function getByPath($gPath)
    {
        $db = Database::connection();
        $row = $db->fetchAssoc('select * from ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' where gPath = ?', [$gPath]);
        if ($row) {
            $g = new static();
            $g->setPropertiesFromArray($row);

            return $g;
        }
    }

    public function export($node)
    {
        $group = $node->addChild('group');
        $group->addAttribute('name', $this->getGroupName());
        $group->addAttribute('description', $this->getGroupDescription());
        $group->addAttribute('path', $this->getGroupPath());
        $group->addAttribute('package', $this->getPackageHandle());
    }

    public function getGroupMembers()
    {
        $user_list = new UserList();
        $user_list->ignorePermissions();
        $user_list->filterByGroup($this);

        return $user_list->get();
    }

    public function getGroupMemberIDs()
    {
        $user_list = new UserList();
        $user_list->ignorePermissions();
        $user_list->filterByGroup($this);

        return $user_list->getResultIDs();
    }

    public function setPermissionsForObject($obj)
    {
        $this->pObj = $obj;
        $db = Database::connection();
        if ($obj instanceof \Concrete\Core\User\UserInfo) {
            $uID = $this->pObj->getUserID();
            if ($uID) {
                $q = "select gID, ugEntered from UserGroups where gID = '{$this->gID}' and uID = {$uID}";
                $r = $db->query($q);
                if ($r) {
                    $row = $r->fetchRow();
                    if ($row['gID']) {
                        $this->inGroup = true;
                        $this->gDateTimeEntered = $row['ugEntered'];
                    }
                }
            }
        }
    }

    public function getGroupMembersNum()
    {
        $user_list = new UserList();
        $user_list->ignorePermissions();
        $user_list->filterByGroup($this);
        return $user_list->getTotalResults();
    }

    /**
     * Deletes a group.
     */
    public function delete()
    {
        // we will NOT let you delete the required groups
        if ($this->gID == REGISTERED_GROUP_ID || $this->gID == GUEST_GROUP_ID) {
            return false;
        }

        // run any internal event we have for group deletion
        $ge = new DeleteEvent($this);
        $ge = Events::dispatch('on_group_delete', $ge);
        if (!$ge->proceed()) {
            return false;
        }

        $tree = GroupTree::get();
        $rootNode = $tree->getRootTreeNodeObject();
        $node = GroupTreeNode::getTreeNodeByGroupID($this->gID);
        if (is_object($node) && is_object($rootNode)) {
            $node->populateDirectChildrenOnly();
            foreach ($node->getChildNodes() as $childnode) {
                $childnode->move($rootNode);
            }
            $node = GroupTreeNode::getTreeNodeByGroupID($this->gID);
            $node->delete();
        }

        $db = Database::connection();
        $db->query('DELETE FROM UserGroups WHERE gID = ?', [intval($this->gID)]);
        $db->query('DELETE FROM ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' WHERE gID = ?', [(int) $this->gID]);
    }

    public function rescanGroupPath()
    {
        $db = Database::connection();
        $path = '';
        // first, we get the group node for this group.
        $node = GroupTreeNode::getTreeNodeByGroupID($this->gID);
        if (is_object($node)) {
            $parents = $node->getTreeNodeParentArray();
            $parents = array_reverse($parents);
            foreach ($parents as $node) {
                $g = $node->getTreeNodeGroupObject();
                if (is_object($g)) {
                    $path .= '/' . $g->getGroupName();
                }
            }
        }

        $path .= '/' . $this->gName;
        $this->gPath = $path;

        $db->executeQuery('update ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' set gPath = ? where gID = ?', [$path, $this->gID]);
    }

    public function rescanGroupPathRecursive()
    {
        $this->rescanGroupPath();

        $node = GroupTreeNode::getTreeNodeByGroupID($this->gID);
        $node->populateDirectChildrenOnly();
        foreach ($node->getChildNodes() as $child) {
            $group = $child->getTreeNodeGroupObject();
            $group->rescanGroupPathRecursive();
        }
    }

    public function inGroup()
    {
        return $this->inGroup;
    }

    /**
     * Get the date/time when a user entered this group.
     *
     * @param object|int $user the user ID or an object with a getUserID method
     *
     * @return string|null
     */
    public function getGroupDateTimeEntered($user)
    {
        if (is_object($user)) {
            $userID = (int) $user->getUserID();
        } elseif (is_numeric($user)) {
            $userID = (int) $user;
        } else {
            $userID = 0;
        }
        $result = null;
        if ($userID !== 0) {
            $db = Application::getFacadeApplication()->make(Connection::class);
            /* @var Connection $db */
            $value = $db->fetchColumn(
                'select ugEntered from UserGroups where gID = ? and uID = ?',
                [$this->gID, $userID]
            );
            if ($value) {
                $result = $value;
            }
        }

        return $result;
    }

    public function getGroupID()
    {
        return $this->gID;
    }

    public function getGroupName()
    {
        return $this->gName;
    }

    public function getGroupPath()
    {
        return $this->gPath;
    }

    public function getParentGroups()
    {
        $node = GroupTreeNode::getTreeNodeByGroupID($this->gID);
        $parentGroups = [];
        if (is_object($node)) {
            $parents = $node->getTreeNodeParentArray();
            $parents = array_reverse($parents);
            foreach ($parents as $node) {
                $g = $node->getTreeNodeGroupObject();
                if (is_object($g)) {
                    $parentGroups[] = $g;
                }
            }
        }

        return $parentGroups;
    }

    public function getChildGroups()
    {
        $node = GroupTreeNode::getTreeNodeByGroupID($this->gID);
        $children = [];
        if (is_object($node)) {
            $node->populateDirectChildrenOnly();
            $node_children = $node->getChildNodes();
            foreach ($node_children as $node_child) {
                $group = $node_child->getTreeNodeGroupObject();
                if (is_object($group)) {
                    $children[] = $group;
                }
            }
        }

        return $children;
    }

    public function getParentGroup()
    {
        $node = GroupTreeNode::getTreeNodeByGroupID($this->gID);
        $parent = $node->getTreeNodeParentObject();
        if ($parent) {
            return $parent->getTreeNodeGroupObject();
        }
    }

    public function getGroupDisplayName($includeHTML = true, $includePath = true)
    {
        $return = '';
        if ($includePath) {
            $parentGroups = $this->getParentGroups();
            if (count($parentGroups) > 0) {
                if ($includeHTML) {
                    $return .= '<span class="ccm-group-breadcrumb">';
                }
                foreach ($parentGroups as $pg) {
                    $return .= h(tc('GroupName', $pg->getGroupName()));
                    $return .= ' ' . Config::get('concrete.seo.group_name_separator') . ' ';
                }
                $return = trim($return);
                if ($includeHTML) {
                    $return .= '</span> ';
                }
            }
        }
        $return .= h(tc('GroupName', $this->getGroupName()));

        return $return;
    }

    public function getGroupDescription()
    {
        return $this->gDescription;
    }

    /**
     * Gets the group start date.
     *
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getGroupStartDate()
    {
        return $this->cgStartDate;
    }

    /**
     * Gets the group end date.
     *
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getGroupEndDate()
    {
        return $this->cgEndDate;
    }

    public function isGroupBadge()
    {
        return $this->gIsBadge;
    }

    public function getGroupBadgeDescription()
    {
        return $this->gBadgeDescription;
    }

    public function getGroupBadgeCommunityPointValue()
    {
        return $this->gBadgeCommunityPointValue;
    }

    public function getGroupBadgeImageID()
    {
        return $this->gBadgeFID;
    }

    public function isGroupAutomated()
    {
        return $this->gIsAutomated;
    }

    public function checkGroupAutomationOnRegister()
    {
        return $this->gCheckAutomationOnRegister;
    }

    public function checkGroupAutomationOnLogin()
    {
        return $this->gCheckAutomationOnLogin;
    }

    public function checkGroupAutomationOnJobRun()
    {
        return $this->gCheckAutomationOnJobRun;
    }

    public function getGroupAutomationController()
    {
        $class = $this->getGroupAutomationControllerClass();
        try {
            $c = \Core::make($class, [$this]);
        } catch (\ReflectionException $e) {
            $c = \Core::make(core_class('\\Core\\User\\Group\\AutomatedGroup\\DefaultAutomation'), [$this]);
        }

        return $c;
    }

    public function getGroupAutomationControllerClass()
    {
        $ts = \Core::make('helper/text');
        $env = \Environment::get();
        $r = $env->getRecord(DIRNAME_CLASSES . '/User/Group/AutomatedGroup/' . camelcase($ts->handle($this->getGroupName())) . '.php');
        $prefix = $r->override ? true : $this->getPackageHandle();
        $class = core_class('\\Core\\User\\Group\\AutomatedGroup\\' . camelcase($ts->handle($this->getGroupName())), $prefix);

        return $class;
    }

    public function getGroupBadgeImageObject()
    {
        $bf = false;
        if ($this->gBadgeFID) {
            $bf = File::getByID($this->gBadgeFID);
            if (!is_object($bf) || $bf->isError()) {
                unset($bf);
            }
        }

        return $bf;
    }

    public function isGroupExpirationEnabled()
    {
        return $this->gUserExpirationIsEnabled;
    }

    public function getGroupExpirationMethod()
    {
        return $this->gUserExpirationMethod;
    }

    public function getGroupExpirationDateTime()
    {
        return $this->gUserExpirationSetDateTime;
    }

    public function getGroupExpirationAction()
    {
        return $this->gUserExpirationAction;
    }

    public function getGroupExpirationInterval()
    {
        return $this->gUserExpirationInterval;
    }

    public function getGroupExpirationIntervalDays()
    {
        return floor($this->gUserExpirationInterval / 1440);
    }

    public function getGroupExpirationIntervalHours()
    {
        return floor(($this->gUserExpirationInterval % 1440) / 60);
    }

    public function getGroupExpirationIntervalMinutes()
    {
        return floor(($this->gUserExpirationInterval % 1440) % 60);
    }

    public function isUserExpired(User $u)
    {
        if ($this->isGroupExpirationEnabled()) {
            switch ($this->getGroupExpirationMethod()) {
                case 'SET_TIME':
                    if (time() > strtotime($this->getGroupExpirationDateTime())) {
                        return true;
                    }
                    break;
                case 'INTERVAL':
                    if (time() > strtotime($this->getGroupDateTimeEntered($u)) + ($this->getGroupExpirationInterval() * 60)) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }

    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    public function update($gName, $gDescription)
    {
        $db = Database::connection();
        if ($this->gID) {
            CacheLocal::delete('group', $this->gID);
            $v = [$gName, $gDescription, $this->gID];
            $r = $db->prepare('update ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' set gName = ?, gDescription = ? where gID = ?');
            $db->Execute($r, $v);
            $group = static::getByID($this->gID);
            $group->rescanGroupPathRecursive();

            $ge = new Event($this);
            Events::dispatch('on_group_update', $ge);

            return $group;
        }
    }

    /** Creates a new user group.
     * @param string $gName
     * @param string $gDescription
     *
     * @return Group
     */
    public static function add($gName, $gDescription, $parentGroup = false, $pkg = null, $gID = null)
    {
        $db = Database::connection();
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $data = [
            'gName' => (string) $gName,
            'gDescription' => (string) $gDescription,
            'pkgID' => (int) $pkgID,
        ];
        if ($gID) {
            $data['gID'] = (int) $gID;
        }
        $db->insert(
            $db->getDatabasePlatform()->quoteSingleIdentifier('Groups'),
            $data
        );
        
        $ng = static::getByID($db->lastInsertId());
        // create a node for this group.
        $node = null;
        if (is_object($parentGroup)) {
            $node = GroupTreeNode::getTreeNodeByGroupID($parentGroup->getGroupID());
        }
        if (!is_object($node)) {
            $tree = GroupTree::get();
            if (is_object($tree)) {
                $node = $tree->getRootTreeNodeObject();
            }
        }

        if (is_object($node)) {
            GroupTreeNode::add($ng, $node);
        }

        $ge = new Event($ng);
        Events::dispatch('on_group_add', $ge);

        $ng->rescanGroupPath();

        return $ng;
    }

    public static function getBadges()
    {
        $gs = new GroupList();
        $gs->filter('gIsBadge', 1);
        $results = $gs->getResults();
        $badges = [];
        foreach ($results as $gr) {
            $badges[] = $gr;
        }

        return $badges;
    }

    protected static function getAutomationControllers($column, $excludeUser = false)
    {
        $gs = new GroupList();
        $gs->filter($column, 1);
        $excludeGIDs = [];
        if (is_object($excludeUser)) {
            $groups = $excludeUser->getUserGroups();
            $groupKeys = array_keys($groups);
            if (is_array($groupKeys)) {
                $gs->filter(false, 'gID not in (' . implode(',', $groupKeys) . ')');
            }
        }
        $results = $gs->get();
        $controllers = [];
        foreach ($results as $group) {
            $controller = $group->getGroupAutomationController();
            $controllers[] = $controller;
        }

        return $controllers;
    }

    public static function getAutomatedOnRegisterGroupControllers($u = false)
    {
        return static::getAutomationControllers('gCheckAutomationOnRegister', $u);
    }

    public static function getAutomatedOnLoginGroupControllers($u = false)
    {
        return static::getAutomationControllers('gCheckAutomationOnLogin', $u);
    }

    public static function getAutomatedOnJobRunGroupControllers()
    {
        return static::getAutomationControllers('gCheckAutomationOnJobRun');
    }

    public function clearBadgeOptions()
    {
        $db = Database::connection();
        $db->executeQuery(
            'update ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' set gIsBadge = 0, gBadgeFID = 0, gBadgeDescription = null, gBadgeCommunityPointValue = 0 where gID = ?',
            [$this->getGroupID()]
        );
    }

    public function clearAutomationOptions()
    {
        $db = Database::connection();
        $db->executeQuery(
            'update ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' set gIsAutomated = 0, gCheckAutomationOnRegister = 0, gCheckAutomationOnLogin = 0, gCheckAutomationOnJobRun = 0 where gID = ?',
            [$this->getGroupID()]
        );
    }

    public function removeGroupExpiration()
    {
        $db = Database::connection();
        $db->executeQuery(
            'update ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' set gUserExpirationIsEnabled = 0, gUserExpirationMethod = null, gUserExpirationSetDateTime = null, gUserExpirationInterval = 0, gUserExpirationAction = null where gID = ?',
            [$this->getGroupID()]
        );
    }

    public function setBadgeOptions($gBadgeFID, $gBadgeDescription, $gBadgeCommunityPointValue)
    {
        $db = Database::connection();
        $db->executeQuery(
            'update ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' set gIsBadge = 1, gBadgeFID = ?, gBadgeDescription = ?, gBadgeCommunityPointValue = ? where gID = ?',
            [intval($gBadgeFID), $gBadgeDescription, $gBadgeCommunityPointValue, $this->gID]
        );
    }

    public function setAutomationOptions(
        $gCheckAutomationOnRegister,
        $gCheckAutomationOnLogin,
        $gCheckAutomationOnJobRun
    ) {
        $db = Database::connection();
        $db->executeQuery(
            'update ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' set gIsAutomated = 1, gCheckAutomationOnRegister = ?, gCheckAutomationOnLogin = ?, gCheckAutomationOnJobRun = ? where gID = ?',
            [
                intval($gCheckAutomationOnRegister),
                intval($gCheckAutomationOnLogin),
                intval($gCheckAutomationOnJobRun),
                $this->gID,
            ]
        );
    }

    public function setGroupExpirationByDateTime($datetime, $action)
    {
        $db = Database::connection();
        $db->executeQuery(
            'update ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' set gUserExpirationIsEnabled = 1, gUserExpirationMethod = \'SET_TIME\', gUserExpirationInterval = 0, gUserExpirationSetDateTime = ?, gUserExpirationAction = ? where gID = ?',
            [$datetime, $action, $this->gID]
        );
    }

    public function setGroupExpirationByInterval($days, $hours, $minutes, $action)
    {
        $db = Database::connection();
        $interval = $minutes + ($hours * 60) + ($days * 1440);
        $db->executeQuery(
            'update ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' set gUserExpirationIsEnabled = 1, gUserExpirationMethod = \'INTERVAL\', gUserExpirationSetDateTime = null, gUserExpirationInterval = ?, gUserExpirationAction = ? where gID = ?',
            [$interval, $action, $this->gID]
        );
    }

    public static function exportTranslations()
    {
        $translations = new Translations();
        $gl = new GroupList();
        $gl->includeAllGroups();
        $results = $gl->getResults();
        foreach ($results as $group) {
            $translations->insert('GroupName', $group->getGroupName());
            if ($group->getGroupDescription()) {
                $translations->insert('GroupDescription', $group->getGroupDescription());
            }
        }

        return $translations;
    }
}
