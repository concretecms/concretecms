<?php

namespace Concrete\Core\Workflow\Progress;

use Core;
use Database;
use Page;
use Concrete\Core\Workflow\Workflow;
use Concrete\Core\Workflow\Request\PageRequest as PageWorkflowRequest;

class PageProgress extends Progress
{
    protected $cID;

    public static function add(Workflow $wf, PageWorkflowRequest $wr)
    {
        $wp = parent::create('page', $wf, $wr);
        $db = Database::connection();
        $db->Replace('PageWorkflowProgress', array('cID' => $wr->getRequestedPageID(), 'wpID' => $wp->getWorkflowProgressID()), array('cID', 'wpID'), true);
        $wp->cID = $wr->getRequestedPageID();

        return $wp;
    }

    public function loadDetails()
    {
        $db = Database::connection();
        $row = $db->GetRow('select cID from PageWorkflowProgress where wpID = ?', array($this->wpID));
        $this->setPropertiesFromArray($row);
    }

    public function delete()
    {
        parent::delete();
        $db = Database::connection();
        $db->Execute('delete from PageWorkflowProgress where wpID = ?', array($this->wpID));
    }

    public static function getList(Page $c, $filters = array('wpIsCompleted' => 0), $sortBy = 'wpDateAdded asc')
    {
        $db = Database::connection();
        $filter = '';
        foreach ($filters as $key => $value) {
            $filter .= ' and ' . $key . ' = ' . $value . ' ';
        }
        $filter .= ' order by ' . $sortBy;
        $r = $db->Execute('select wp.wpID from PageWorkflowProgress pwp inner join WorkflowProgress wp on pwp.wpID = wp.wpID where cID = ? ' . $filter, array($c->getCollectionID()));
        $list = array();
        while ($row = $r->FetchRow()) {
            $wp = static::getByID($row['wpID']);
            if (is_object($wp)) {
                $list[] = $wp;
            }
        }

        return $list;
    }

    public function getWorkflowProgressFormAction()
    {
        return REL_DIR_FILES_TOOLS_REQUIRED . '/' . DIRNAME_WORKFLOW . '/categories/page?task=save_workflow_progress&cID=' . $this->cID . '&wpID=' . $this->getWorkflowProgressID() . '&' . Core::make('helper/validation/token')->getParameter('save_workflow_progress');
    }

    public function getPendingWorkflowProgressList()
    {
        $list = new \Concrete\Core\Page\Workflow\Progress\ProgressList();
        $list->filter('wpApproved', 0);
        $list->sortBy('wpDateLastAction', 'desc');

        return $list;
    }
}
