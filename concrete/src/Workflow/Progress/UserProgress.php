<?php
namespace Concrete\Core\Workflow\Progress;

use Concrete\Core\Workflow\Request\UserRequest;
use Loader;
use \Concrete\Core\Workflow\Workflow;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Validation\CSRF\Token;

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
        $url = app(ResolverManagerInterface::class)->resolve(['/ccm/system/workflow/categories/user/save_progress']);
        $token = app(Token::class);
        $query = $url->getQuery();
        $query->modify([
            'uID' => $this->uID,
            'wpID' => $this->getWorkflowProgressID(),
            $token::DEFAULT_TOKEN_NAME => $token->generate('save_user_workflow_progress')
        ]);
        return (string) $url->setQuery($query);
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
        while ($row = $r->fetch()) {
            $wp = UserProgress::getByID($row['wpID']);
            if (is_object($wp)) {
                $list[] = $wp;
            }
        }

        return $list;
    }
}



