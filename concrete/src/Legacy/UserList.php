<?php

namespace Concrete\Core\Legacy;

use Concrete\Core\Legacy\DatabaseItemList;
use UserAttributeKey;
use UserInfo;
use Core;
use Database;
use Group;

/**
 * An object that allows a filtered list of users to be returned.
 * @package Files
 * @author Tony Trupp <tony@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class UserList extends DatabaseItemList
{
    protected $attributeFilters = array();
    protected $autoSortColumns = array('uName', 'uEmail', 'uDateAdded', 'uLastLogin', 'uNumLogins', 'uLastOnline');
    protected $itemsPerPage = 10;
    protected $attributeClass = 'UserAttributeKey';

    public $showInactiveUsers;
    public $showInvalidatedUsers = 0;
    public $searchAgainstEmail = 0;

    //Filter by uName
    public function filterByUserName($username)
    {
        $this->filter('u.uName', $username, '=');
    }

    public function filterByKeywords($keywords)
    {
        $db = Database::connection();
        $qkeywords = $db->quote('%' . $keywords . '%');
        $keys = UserAttributeKey::getSearchableIndexedList();
        $emailSearchStr = ' OR u.uEmail like '.$qkeywords.' ';
        $attribsStr = '';
        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $attribsStr .= ' OR ' . $cnt->searchKeywords($keywords);
        }
        $this->filter(false, '( u.uName like ' . $qkeywords . $emailSearchStr . $attribsStr . ')');
    }

    /**
     * Filters the user list for only users within the provided group.  Accepts an instance of a group object or a string group name.
     *
     * @param Group|string $group
     * @param bool $inGroup
     */
    public function filterByGroup($group = '', $inGroup = true)
    {
        if (!$group instanceof Group) {
            $group = Group::getByName($group);
        }
        $tbl = 'ug_'.$group->getGroupID();
        $this->addToQuery("left join UserGroups $tbl on {$tbl}.uID = u.uID ");
        if ($inGroup) {
            $this->filter(false, "{$tbl}.gID=".intval($group->getGroupID()));
        } else {
            $this->filter(false, "{$tbl}.gID is null");
        }
    }

    public function excludeUsers($uo)
    {
        if (is_object($uo)) {
            $uID = $uo->getUserID();
        } else {
            $uID = $uo;
        }
        $this->filter('u.uID', $uID, '!=');
    }

    public function filterByGroupID($gID)
    {
        if (!Core::make('helper/validation/numbers')->integer($gID)) {
            $gID = 0;
        }
        $tbl = 'ug_'.$gID;
        $this->addToQuery("left join UserGroups $tbl on {$tbl}.uID = u.uID ");
        $this->filter(false, "{$tbl}.gID=".$gID);
    }

    public function filterByDateAdded($date, $comparison = '=')
    {
        $this->filter('u.uDateAdded', $date, $comparison);
    }

    /**
     * Returns an array of userInfo objects based on current filter settings.
     *
     * @return UserInfo[]
     */
    public function get($itemsToGet = 100, $offset = 0)
    {
        $userInfos = array();
        $this->createQuery();
        $r = parent::get($itemsToGet, intval($offset));
        foreach ($r as $row) {
            $ui = UserInfo::getByID($row['uID']);
            $userInfos[] = $ui;
        }

        return $userInfos;
    }

    /**
     * Similar to get except it returns an array of userIDs.
     * Much faster than getting a UserInfo object for each result if all you need is the user's id.
     *
     * @return array $userIDs
     */
    public function getUserIDs($itemsToGet = 100, $offset = 0)
    {
        $this->createQuery();
        $userIDs = array();
        $r = parent::get($itemsToGet, intval($offset));
        foreach ($r as $row) {
            $userIDs[] = $row['uID'];
        }

        return $userIDs;
    }

    public function getTotal()
    {
        $this->createQuery();

        return parent::getTotal();
    }

    public function filterByIsActive($val)
    {
        $this->showInactiveUsers = $val;
        $this->filter('u.uIsActive', $val);
    }

    //this was added because calling both getTotal() and get() was duplicating some of the query components
    protected function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            if (!isset($this->showInactiveUsers)) {
                $this->filter('u.uIsActive', 1);
            }
            if (!$this->showInvalidatedUsers) {
                $this->filter('u.uIsValidated', 0, '!=');
            }
            $this->setupAttributeFilters("left join UserSearchIndexAttributes on (UserSearchIndexAttributes.uID = u.uID)");
            $this->queryCreated = 1;
        }
    }

    protected function setBaseQuery()
    {
        $this->setQuery('SELECT DISTINCT u.uID, u.uName FROM Users u ');
    }

    /* magic method for filtering by page attributes. */
    public function __call($nm, $a)
    {
        if (substr($nm, 0, 8) == 'filterBy') {
            $txt = Core::make('helper/text');
            $attrib = $txt->uncamelcase(substr($nm, 8));
            if (count($a) == 2) {
                $this->filterByAttribute($attrib, $a[0], $a[1]);
            } else {
                $this->filterByAttribute($attrib, $a[0]);
            }
        }
    }
}
