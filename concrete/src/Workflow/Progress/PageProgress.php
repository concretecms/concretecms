<?php
namespace Concrete\Core\Workflow\Progress;

use Core;
use Database;
use Page;
use Concrete\Core\Workflow\Workflow;
use Concrete\Core\Workflow\Request\PageRequest as PageWorkflowRequest;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Validation\CSRF\Token;

class PageProgress extends Progress implements SiteProgressInterface
{
    protected $cID;

    public function getSite()
    {
        $page = \Page::getByID($this->cID);
        return $page->getSite();
    }

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
        while ($row = $r->fetch()) {
            $wp = static::getByID($row['wpID']);
            if (is_object($wp)) {
                $list[] = $wp;
            }
        }

        return $list;
    }

    public function getWorkflowProgressFormAction()
    {
        $url = app(ResolverManagerInterface::class)->resolve(['/ccm/system/workflow/categories/page/save_progress']);
        $token = app(Token::class);
        $query = $url->getQuery();
        $query->modify([
            'cID' => $this->cID,
            'wpID' => $this->getWorkflowProgressID(),
            $token::DEFAULT_TOKEN_NAME => $token->generate('save_workflow_progress')
        ]);
        
        return (string) $url->setQuery($query);
    }

    public function getPendingWorkflowProgressList()
    {
        $list = new \Concrete\Core\Page\Workflow\Progress\ProgressList();
        $list->filter('wpApproved', 0);
        $list->sortBy('wpDateLastAction', 'desc');

        return $list;
    }
}
