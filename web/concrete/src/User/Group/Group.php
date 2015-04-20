<?php
namespace Concrete\Core\User\Group;

use \Concrete\Core\Foundation\Object;
use Concrete\Core\User\User;
use Config;
use Gettext\Translations;
use Loader;
use CacheLocal;
use GroupTree;
use GroupTreeNode;
use Environment;
use UserList;
use Events;
use \Concrete\Core\Package\PackageList;
use File;

class Group extends Object implements \Concrete\Core\Permission\ObjectInterface
{

    var $ctID;
    var $permissionSet;
    private $permissions = array(); // more advanced version of permissions

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
     * Takes the numeric id of a group and returns a group object
     * @param string $gID
     * @return Group
     */
    public static function getByID($gID)
    {
        $db = Loader::db();
        $g = CacheLocal::getEntry('group', $gID);
        if (is_object($g)) {
            return $g;
        }

        $row = $db->getRow("select * from Groups where gID = ?", array($gID));
        if (isset($row['gID'])) {
            $g = \Core::make('\Concrete\Core\User\Group\Group');
            $g->setPropertiesFromArray($row);
            CacheLocal::set('group', $gID, $g);
            return $g;
        }
    }

    /**
     * Takes the name of a group and returns a group object
     * @param string $gName
     * @return Group
     */
    public static function getByName($gName)
    {
        $db = Loader::db();
        $row = $db->getRow("select * from Groups where gName = ?", array($gName));
        if (isset($row['gID'])) {
            $g = new Group;
            $g->setPropertiesFromArray($row);
            return $g;
        }
    }

    /**
     * @param string $gPath The group path
     * @return Group
     */
    public static function getByPath($gPath)
    {
        $db = Loader::db();
        $row = $db->getRow("select * from Groups where gPath = ?", array($gPath));
        if (isset($row['gID'])) {
            $g = new Group;
            $g->setPropertiesFromArray($row);
            return $g;
        }
    }

    public function getGroupMembers()
    {
        $user_list = new UserList();
        $user_list->filterByGroup($this);

        return $user_list->get();
    }

    public function getGroupMemberIDs()
    {
        $user_list = new UserList();
        $user_list->filterByGroup($this);

        return $user_list->getResultIDs();
    }

    public function setPermissionsForObject($obj)
    {
        $this->pObj = $obj;
        $db = Loader::db();
        if ($obj instanceof UserInfo) {
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
        $db = Loader::db();
        $cnt = $db->GetOne("select count(uID) from UserGroups where gID = ?", array($this->gID));
        return $cnt;
    }


    /**
     * Deletes a group
     * @return void
     */
    function delete()
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

        $db = Loader::db();
        $r = $db->query("DELETE FROM UserGroups WHERE gID = ?", array(intval($this->gID)));
        $r = $db->query("DELETE FROM Groups WHERE gID = ?", array(intval($this->gID)));
    }

    public function rescanGroupPath()
    {
        $db = Loader::db();
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
        $db->Execute('update Groups set gPath = ? where gID = ?', array($path, $this->gID));
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

    function inGroup()
    {
        return $this->inGroup;
    }

    function getGroupDateTimeEntered($user)
    {
        $db = Loader::db();
        $q = "select ugEntered from UserGroups where gID = ? and uID = ?";
        $r = $db->GetOne($q, array($this->gID, $user->getUserID()));
        if ($r) {
            return $r;
        }
    }

    function getGroupID()
    {
        return $this->gID;
    }

    function getGroupName()
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
        $parentGroups = array();
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
        $children = array();
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
        $return .= tc('GroupName', $this->getGroupName());
        return $return;
    }

    function getGroupDescription()
    {
        return $this->gDescription;
    }

    /**
     * Gets the group start date
     * @return string date formated like: 2009-01-01 00:00:00
     */
    function getGroupStartDate()
    {
        return $this->cgStartDate;
    }

    /**
     * Gets the group end date
     * @return string date formated like: 2009-01-01 00:00:00
     */
    function getGroupEndDate()
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
            $c = \Core::make($class, array($this));
        } catch(\ReflectionException $e) {
            $c = \Core::make(core_class('\\Core\\User\\Group\\AutomatedGroup\\DefaultAutomation'), array($this));
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

    function update($gName, $gDescription)
    {
        $db = Loader::db();
        if ($this->gID) {
            $g = CacheLocal::delete('group', $this->gID);
            $v = array($gName, $gDescription, $this->gID);
            $r = $db->prepare("update Groups set gName = ?, gDescription = ? where gID = ?");
            $res = $db->Execute($r, $v);
            $group = Group::getByID($this->gID);
            $group->rescanGroupPathRecursive();

            $ge = new Event($this);
            Events::dispatch('on_group_update', $ge);

            return $group;
        }
    }

    /** Creates a new user group.
     * @param string $gName
     * @param string $gDescription
     * @return Group
     */
    public static function add($gName, $gDescription, $parentGroup = false, $pkg = null, $gID = null)
    {
        $db = Loader::db();
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $v = array($gID, $gName, $gDescription, $pkgID);
        $r = $db->prepare("insert into Groups (gID, gName, gDescription, pkgID) values (?, ?, ?, ?)");
        $res = $db->Execute($r, $v);

        if ($res) {
            $ng = Group::getByID($db->Insert_ID());
            // create a node for this group.

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
    }

    public static function getBadges()
    {
        $gs = new GroupList();
        $gs->filter('gIsBadge', 1);
        $results = $gs->getResults();
        $badges = array();
        foreach ($results as $gr) {
            $badges[] = $gr;
        }
        return $badges;
    }

    protected function getAutomationControllers($column, $excludeUser = false)
    {
        $gs = new GroupList();
        $gs->filter($column, 1);
        $excludeGIDs = array();
        if (is_object($excludeUser)) {
            $groups = $excludeUser->getUserGroups();
            $groupKeys = array_keys($groups);
            if (is_array($groupKeys)) {
                $gs->filter(false, 'gID not in (' . implode(',', $groupKeys) . ')');
            }
        }
        $results = $gs->get();
        $controllers = array();
        foreach ($results as $group) {
            $controller = $group->getGroupAutomationController();
            $controllers[] = $controller;
        }
        return $controllers;
    }

    public static function getAutomatedOnRegisterGroupControllers($u = false)
    {
        return Group::getAutomationControllers('gCheckAutomationOnRegister', $u);
    }

    public static function getAutomatedOnLoginGroupControllers($u = false)
    {
        return Group::getAutomationControllers('gCheckAutomationOnLogin', $u);
    }

    public static function getAutomatedOnJobRunGroupControllers()
    {
        return Group::getAutomationControllers('gCheckAutomationOnJobRun');
    }

    public function clearBadgeOptions()
    {
        $db = Loader::db();
        $db->Execute(
            'update Groups set gIsBadge = 0, gBadgeFID = 0, gBadgeDescription = null, gBadgeCommunityPointValue = 0 where gID = ?',
            array($this->getGroupID())
        );
    }

    public function clearAutomationOptions()
    {
        $db = Loader::db();
        $db->Execute(
            'update Groups set gIsAutomated = 0, gCheckAutomationOnRegister = 0, gCheckAutomationOnLogin = 0, gCheckAutomationOnJobRun = 0 where gID = ?',
            array($this->getGroupID())
        );
    }

    public function removeGroupExpiration()
    {
        $db = Loader::db();
        $db->Execute(
            'update Groups set gUserExpirationIsEnabled = 0, gUserExpirationMethod = null, gUserExpirationSetDateTime = null, gUserExpirationInterval = 0, gUserExpirationAction = null where gID = ?',
            array($this->getGroupID())
        );
    }

    public function setBadgeOptions($gBadgeFID, $gBadgeDescription, $gBadgeCommunityPointValue)
    {
        $db = Loader::db();
        $db->Execute(
            'update Groups set gIsBadge = 1, gBadgeFID = ?, gBadgeDescription = ?, gBadgeCommunityPointValue = ? where gID = ?',
            array(intval($gBadgeFID), $gBadgeDescription, $gBadgeCommunityPointValue, $this->gID)
        );
    }

    public function setAutomationOptions(
        $gCheckAutomationOnRegister,
        $gCheckAutomationOnLogin,
        $gCheckAutomationOnJobRun
    ) {
        $db = Loader::db();
        $db->Execute(
            'update Groups set gIsAutomated = 1, gCheckAutomationOnRegister = ?, gCheckAutomationOnLogin = ?, gCheckAutomationOnJobRun = ? where gID = ?',
            array(
                intval($gCheckAutomationOnRegister),
                intval($gCheckAutomationOnLogin),
                intval($gCheckAutomationOnJobRun),
                $this->gID
            )
        );
    }

    public function setGroupExpirationByDateTime($datetime, $action)
    {
        $db = Loader::db();
        $db->Execute(
            'update Groups set gUserExpirationIsEnabled = 1, gUserExpirationMethod = \'SET_TIME\', gUserExpirationInterval = 0, gUserExpirationSetDateTime = ?, gUserExpirationAction = ? where gID = ?',
            array($datetime, $action, $this->gID)
        );
    }

    public function setGroupExpirationByInterval($days, $hours, $minutes, $action)
    {
        $db = Loader::db();
        $interval = $minutes + ($hours * 60) + ($days * 1440);
        $db->Execute(
            'update Groups set gUserExpirationIsEnabled = 1, gUserExpirationMethod = \'INTERVAL\', gUserExpirationSetDateTime = null, gUserExpirationInterval = ?, gUserExpirationAction = ? where gID = ?',
            array($interval, $action, $this->gID)
        );
    }

    public static function exportTranslations()
    {
        $translations = new Translations();
        $gl = new GroupList();
        $gl->includeAllGroups();
        $results = $gl->getResults();
        foreach($results as $group) {
            $translations->insert('GroupName', $group->getGroupName());
            if ($group->getGroupDescription()) {
                $translations->insert('GroupDescription', $group->getGroupDescription());
            }
        }
        return $translations;
    }

}
