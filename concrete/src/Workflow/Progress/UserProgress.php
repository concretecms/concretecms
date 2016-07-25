<?php
namespace Concrete\Core\Workflow\Progress;

use Concrete\Core\Workflow\Request\UserRequest;
use Loader;
use \Concrete\Core\Workflow\Workflow;

class UserProgress extends Progress
{

    // Notice: here uID is requested user id, NOT requester user id
    protected $uID;

    public function loadDetails()
    {
        $db = Loader::db();
        $row = $db->GetRow('select uID from UserWorkflowProgress where wpID = ?', array($this->wpID));
        $this->setPropertiesFromArray($row);
    }

    public function delete()
    {
        parent::delete();
        $db = Loader::db();
        $db->Execute('delete from UserWorkflowProgress where wpID = ?', array($this->wpID));
    }

    public static function add(Workflow $wf, UserRequest $wr)
    {
        $wp = parent::add('user', $wf, $wr);
        $db = Loader::db();
        $db->Replace('UserWorkflowProgress',
            array('uID' => $wr->getRequestedUserID(), 'wpID' => $wp->getWorkflowProgressID()), array('uID', 'wpID'),
            true);
        $wp->uID = $wr->getRequestedUserID();

        return $wp;
    }

    public function getWorkflowProgressFormAction()
    {
        return REL_DIR_FILES_TOOLS_REQUIRED . '/' . DIRNAME_WORKFLOW . '/categories/user?task=save_user_workflow_progress&uID=' . $this->uID . '&wpID=' . $this->getWorkflowProgressID() . '&' . Loader::helper('validation/token')->getParameter('save_user_workflow_progress');
    }

    public function getPendingWorkflowProgressList()
    {
        $list = new \Concrete\Core\User\Workflow\Progress\ProgressList();
        $list->filter('wpApproved', 0);
        $list->sortBy('wpDateLastAction', 'desc');

        return $list;
    }

    public static function getList($requestedUID, $filters = array('wpIsCompleted' => 0), $sortBy = 'wpDateAdded asc')
    {
        $db = Loader::db();

        $filter = '';
        foreach ($filters as $key => $value) {
            $filter .= ' and ' . $key . ' = ' . $value . ' ';
        }
        $filter .= ' order by ' . $sortBy;


        $r = $db->Execute('SELECT wp.wpID FROM UserWorkflowProgress uwp INNER JOIN WorkflowProgress wp ON wp.wpID = uwp.wpID WHERE uwp.uID = ? ' . $filter,
            $requestedUID);
        $list = array();
        while ($row = $r->FetchRow()) {
            $wp = UserProgress::getByID($row['wpID']);
            if (is_object($wp)) {
                $list[] = $wp;
            }
        }

        return $list;
    }
}



