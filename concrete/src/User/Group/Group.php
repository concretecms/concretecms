<?php

namespace Concrete\Core\User\Group;

use CacheLocal;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Notification\GroupRoleChangeNotification;
use Concrete\Core\Entity\Notification\GroupSignupRequestNotification;
use Concrete\Core\Entity\User\GroupRoleChange;
use Concrete\Core\Entity\User\GroupSignupRequest;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Notification\Type\GroupRoleChangeType;
use Concrete\Core\Notification\Type\GroupSignupRequestType;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\User\Group\Command\AddGroupCommand;
use Concrete\Core\User\Group\Command\DeleteGroupCommand;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Config;
use Database;
use Doctrine\DBAL\Exception;
use Events;
use File;
use Gettext\Translations;
use GroupTree;
use GroupTreeNode;
use Concrete\Core\User\UserList;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Messenger\MessageBusInterface;

class Group extends ConcreteObject implements \Concrete\Core\Permission\ObjectInterface, \JsonSerializable
{

    public $gID = 0;

    public $gIsBadge = false;

    public $gName;

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

    public function getGroupMembersNum()
    {
        $user_list = new UserList();
        $user_list->ignorePermissions();
        $user_list->filterByGroup($this);
        return $user_list->getTotalResults();
    }

    /**
     * @deprecated
     * Deletes a group. This is deprecated – use the DeleteGroupCommand and the command bus.
     */
    public function delete()
    {
        $app = Facade::getFacadeApplication();
        $command = new DeleteGroupCommand($this->getGroupID());
        return $app->executeCommand($command);
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
                if ($node instanceof \Concrete\Core\Tree\Node\Type\Group) {
                    $g = $node->getTreeNodeGroupObject();
                    if (is_object($g)) {
                        $path .= '/' . $g->getGroupName();
                    }
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
            if ($child instanceof \Concrete\Core\Tree\Node\Type\Group) {
                $group = $child->getTreeNodeGroupObject();
                if ($group instanceof Group) {
                    $group->rescanGroupPathRecursive();
                }
            }
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
            $userID = (int)$user->getUserID();
        } elseif (is_numeric($user)) {
            $userID = (int)$user;
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

    public function getOverrideGroupTypeSettings()
    {
        return (bool)$this->gOverrideGroupTypeSettings;
    }

    /**
     * @param User $user
     * @param GroupRole $userRole
     * @return bool
     */
    public function changeUserRole($user, $userRole)
    {
        $activeUser = new User();

        if ($this->hasUserManagerPermissions($activeUser) && $user->inGroup($this)) {
            if ($user->isRegistered() && $this->isPetitionForPublicEntry()) {
                $app = Application::getFacadeApplication();
                /** @var Connection $db */
                $db = $app->make(Connection::class);
                $db->executeQuery("UPDATE UserGroups SET grID = ? WHERE gID = ? AND uID = ?", [$userRole->getId(), $this->getGroupID(), $user->getUserID()]);

                /** @noinspection PhpUnhandledExceptionInspection */
                $subject = new GroupRoleChange($this, $user, $userRole);
                /** @var GroupRoleChangeType $type */
                $type = $app->make('manager/notification/types')->driver('group_role_change');
                $notifier = $type->getNotifier();
                if (method_exists($notifier, 'notify')) {
                    $subscription = $type->getSubscription($subject);
                    $users = $notifier->getUsersToNotify($subscription, $subject);
                    $notification = new GroupRoleChangeNotification($subject);
                    $notifier->notify($users, $notification);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function sendJoinRequest()
    {
        $user = new User();
        if ($user->isRegistered() && $this->isPetitionForPublicEntry()) {
            $app = Application::getFacadeApplication();
            /** @var Connection $db */
            $db = $app->make(Connection::class);
            $dt = $app->make('helper/date');

            $db->executeQuery("DELETE FROM GroupJoinRequests WHERE gID = ? AND uID = ?", [$this->getGroupID(), $user->getUserID()]);
            $db->insert("GroupJoinRequests", [
                "gID" => $this->getGroupID(),
                "uID" => $user->getUserID(),
                "gjrRequested" => $dt->getOverridableNow()
            ]);

            /** @noinspection PhpUnhandledExceptionInspection */
            $subject = new GroupSignupRequest($this, $user);
            /** @var GroupSignupRequestType $type */
            $type = $app->make('manager/notification/types')->driver('group_signup_request');
            $notifier = $type->getNotifier();

            if (method_exists($notifier, 'notify')) {
                $subscription = $type->getSubscription($subject);
                $users = $notifier->getUsersToNotify($subscription, $subject);
                $notification = new GroupSignupRequestNotification($subject);
                $notifier->notify($users, $notification);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return GroupJoinRequest[]
     */
    public function getJoinRequests()
    {
        $joinRequests = [];

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        /** @var UserInfoRepository $userInfoRepository */
        $userInfoRepository = $app->make(UserInfoRepository::class);

        foreach ($db->fetchAll("SELECT uID FROM GroupJoinRequests WHERE gID = ?", [$this->getGroupID()]) as $row) {
            $userInfo = $userInfoRepository->getByID($row["uID"]);
            $userObject = $userInfo->getUserObject();
            $joinRequests[] = new GroupJoinRequest($this, $userObject);
        }

        return $joinRequests;
    }


    public function setOverrideGroupTypeSettings($gOverrideGroupTypeSettings)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->gOverrideGroupTypeSettings = $gOverrideGroupTypeSettings;

        try {
            $db->executeQuery("update `Groups` set gOverrideGroupTypeSettings = ? where gID = ?", [(int)$gOverrideGroupTypeSettings, $this->getGroupID()]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getGroupTypeId()
    {
        return $this->gtID;
    }

    /**
     * @return bool|GroupType
     */
    public function getGroupType()
    {
        if (is_object(GroupType::getByID($this->gtID))) {
            return GroupType::getByID($this->gtID);
        } else {
            return GroupType::getByID(DEFAULT_GROUP_TYPE_ID);
        }
    }

    /**
     * @return bool|GroupType
     */
    public function setGroupType(GroupType $groupType)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->gtID = $groupType->getId();

        try {
            $db->executeQuery("update `Groups` set gtID = ? where gID = ?", [$this->gtID, $this->getGroupID()]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getDefaultRoleId()
    {
        return $this->gDefaultRoleID;
    }

    /**
     * @return GroupRole
     */
    public function getDefaultRole()
    {
        if ($this->getOverrideGroupTypeSettings()) {
            return GroupRole::getByID($this->gDefaultRoleID);
        } else {
            if (is_object($this->getGroupType())) {
                return $this->getGroupType()->getDefaultRole();
            } else {
                return null;
            }
        }
    }

    /**
     * @param GroupRole $role
     * @return bool
     */
    public function setDefaultRole($role)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->gDefaultRoleID = $role->getId();

        try {
            $db->executeQuery("update `Groups` set gDefaultRoleID = ? where gID = ?", [(int)$this->gDefaultRoleID, (int)$this->getGroupID()]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return \Concrete\Core\Entity\File\File|bool
     */
    public function getThumbnailImage()
    {
        $bf = false;

        if ($this->gThumbnailFID) {
            $bf = \Concrete\Core\File\File::getByID($this->gThumbnailFID);
            if (!is_object($bf) || $bf->isError()) {
                unset($bf);
            }
        }

        return $bf;
    }

    public function removeThumbnailImage()
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $db->executeQuery("update `Groups` set gThumbnailFID = ? where gID = ?", [0, $this->getGroupID()]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param \Concrete\Core\Entity\File\File $file
     * @return bool
     */
    public function setThumbnailImage(\Concrete\Core\Entity\File\File $file)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->gThumbnailFID = $file->getFileID();

        try {
            $db->executeQuery("update `Groups` set gThumbnailFID = ? where gID = ?", [$this->gThumbnailFID, $this->getGroupID()]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function isPetitionForPublicEntry()
    {
        if ($this->getOverrideGroupTypeSettings()) {
            return (bool)$this->gPetitionForPublicEntry;
        } else {
            return (bool)$this->getGroupType()->isPetitionForPublicEntry();
        }
    }

    public function setPetitionForPublicEntry($gPetitionForPublicEntry)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->gPetitionForPublicEntry = $gPetitionForPublicEntry;

        try {
            $db->executeQuery("update `Groups` set gPetitionForPublicEntry = ? where gID = ?", [(int)$gPetitionForPublicEntry, $this->getGroupID()]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasUserManagerPermissions($user) {
        if ($user->isRegistered() && $user->isSuperUser()) {
            return true; // super-admin
        } else if ($this->getAuthorID() == $user->getUserID()) {
            return true; // owner
        } else {
            $userRole = $this->getUserRole($user);
            if (is_object($userRole)) {
                return $userRole->isManager();
            } else {
                return false;
            }
        }
    }

    /**
     * @param User $user
     * @return GroupRole|null
     */
    public function getUserRole($user)
    {
        if ($user->isRegistered()) {
            $app = Application::getFacadeApplication();
            /** @var Connection $db */
            $db = $app->make(Connection::class);
            $row = $db->fetchAssoc("SELECT grID FROM UserGroups WHERE gID = ? AND uID = ?", [$this->getGroupID(), $user->getUserID()]);
            if (isset($row)) {
                return GroupRole::getByID($row["grID"]);
            }
        }

        return null;
    }

    /**
     * @return GroupRole[]
     */
    public function getRoles()
    {
        if ($this->getOverrideGroupTypeSettings()) {
            return GroupRole::getListByGroup($this);
        } else {
            return GroupRole::getListByGroupType($this->getGroupType());
        }
    }

    /**
     * @param GroupRole $role
     * @return bool
     */
    public function addRole($role)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $db->executeQuery('insert into GroupSelectedRoles (grID, gID) values (?,?)', [(int)$role->getId(), (int)$this->getGroupID()]);
        } catch (Exception $e) {
            return false;
        }

        return true;
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
                if ($node instanceof \Concrete\Core\Tree\Node\Type\Group) {
                    $g = $node->getTreeNodeGroupObject();
                    if (is_object($g)) {
                        $parentGroups[] = $g;
                    }
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
                if ($node_child instanceof \Concrete\Core\Tree\Node\Type\Group) {
                    $group = $node_child->getTreeNodeGroupObject();
                    if (is_object($group)) {
                        $children[] = $group;
                    }
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

    /**
     * @return mixed
     * @deprecated
     */
    public function isGroupBadge()
    {
        return $this->gIsBadge;
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function getGroupBadgeDescription()
    {
        return $this->gBadgeDescription;
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function getGroupBadgeCommunityPointValue()
    {
        return $this->gBadgeCommunityPointValue;
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function getGroupBadgeImageID()
    {
        return $this->gBadgeFID;
    }

    public function getAuthorID()
    {
        return $this->gAuthorID;
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
            $c = \Core::make($class, ['g' => $this]);
        } catch (BindingResolutionException $e) {
            $c = \Core::make(core_class('\\Core\\User\\Group\\AutomatedGroup\\DefaultAutomation'), ['g' => $this]);
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

    /**
     * @return bool
     * @deprecated
     */
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
     * @deprecated
     * This is deprecated; use the AddGroupCommand and the command bus.
     */
    public static function add($gName, $gDescription, $parentGroup = false, $pkg = null)
    {
        $app = Facade::getFacadeApplication();
        $command = new AddGroupCommand();
        $command->setName($gName);
        $command->setDescription($gDescription);
        if ($parentGroup) {
            $command->setParentGroupID($parentGroup->getGroupID());
        }
        if ($pkg) {
            $command->setPackageID($pkg->getPackageID());
        }
        return $app->executeCommand($command);
    }

    /** Creates a new user group.
     *
     * This is deprecated; use the AddGroupCommand and the command bus.
     * @param string $gName
     * @param string $gDescription
     * @param GroupFolder $parentFolder
     *
     * @return Group
     */
    public static function addBeneathFolder($gName, $gDescription, $parentFolder = false, $pkg = null)
    {
        $app = Facade::getFacadeApplication();
        $command = new AddGroupCommand();
        $command->setName($gName);
        $command->setDescription($gDescription);
        if ($parentFolder) {
            $command->setParentNodeID($parentFolder->getTreeNodeID());
        }

        if ($pkg) {
            $command->setPackageID($pkg->getPackageID());
        }
        return $app->executeCommand($command);
    }

    /**
     * @deprecated
     */
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

    /**
     * @deprecated
     */
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

    /**
     * @param $gBadgeFID
     * @param $gBadgeDescription
     * @param $gBadgeCommunityPointValue
     * @deprecated
     */
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
    )
    {
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

    /**
     * Takes the numeric id of a group and returns a group object.
     * @param string $gID
     *
     * @return Group
     * @deprecated
     * This is deprecated, user the grouprepository instead.
     */
    public static function getByID($gID)
    {
        $app = Facade::getFacadeApplication();
        $repository = $app->make(GroupRepository::class);
        return $repository->getGroupByID($gID);
    }

    /**
     * Takes the name of a group and returns a group object.
     * @param string $gName
     *
     * @return Group
     * @deprecated
     * This is deprecated, user the grouprepository instead.
     */
    public static function getByName($gName)
    {
        $app = Facade::getFacadeApplication();
        $repository = $app->make(GroupRepository::class);
        return $repository->getGroupByName($gName);
    }

    /**
     * @param string $gPath The group path
     * @return Group
     * @deprecated
     * This is deprecated, user the grouprepository instead.
     */
    public static function getByPath($gPath)
    {
        $app = Facade::getFacadeApplication();
        $repository = $app->make(GroupRepository::class);
        return $repository->getGroupByPath($gPath);
    }

    /**
     * @return mixed|void
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'gID' => $this->getGroupID(),
            'gName' => $this->getGroupName(),
            'gDisplayName' => $this->getGroupDisplayName(false)
        ];
    }

}
