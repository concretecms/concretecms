<?php
namespace Concrete\Core\User\Workflow\Progress;

use Concrete\Core\Legacy\DatabaseItemList;
use Concrete\Core\Legacy\UserList;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\Workflow\Progress\User as ProgressUser;
use \Concrete\Core\Workflow\Progress\UserProgress as UserWorkflowProgress;

class ProgressList extends UserList
{

    protected $autoSortColumns = array('uName', 'wpDateLastAction', 'wpCurrentStatus');

    public function __construct()
    {
        $this->setQuery('SELECT DISTINCT u.uID, u.uName, wp.wpID, wp.wpDateLastAction, wp.wpCurrentStatus FROM Users u INNER JOIN UserWorkflowProgress uwp ON uwp.uID = u.uID INNER JOIN WorkflowProgress wp ON wp.wpID = uwp.wpID');
        $this->filter('wpIsCompleted', 0);
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $_users = DatabaseItemList::get($itemsToGet, $offset);
        $users = array();
        foreach ($_users as $row) {
            $u = UserInfo::getByID($row['uID']);
            $wp = UserWorkflowProgress::getByID($row['wpID']);
            $users[] = new ProgressUser($u, $wp);
        }

        return $users;
    }

}