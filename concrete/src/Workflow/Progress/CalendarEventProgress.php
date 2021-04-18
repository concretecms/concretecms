<?php
namespace Concrete\Core\Workflow\Progress;

use Concrete\Core\Calendar\Event\Workflow\Progress\ProgressList;
use Concrete\Core\Workflow\Workflow;
use Concrete\Core\Workflow\Request\CalendarEventRequest;
use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Validation\CSRF\Token;

class CalendarEventProgress extends Progress implements SiteProgressInterface
{
    protected $eventID;

    public function getSite()
    {
        $event = Event::getByID($this->eventID);
        if (is_object($event)) {
            $calendar = $event->getCalendar();
            if (is_object($calendar)) {
                return $calendar->getSite();
            }
        }
    }

    public static function add(Workflow $wf, CalendarEventRequest $wr)
    {
        $wp = parent::create('calendar_event', $wf, $wr);
        $version = $wr->getRequestedEventVersionObject();

        $db = \Database::connection();
        $db->Replace('CalendarEventWorkflowProgress', array('eventID' => $version->getEvent()->getID(), 'wpID' => $wp->getWorkflowProgressID()), array('eventID', 'wpID'), true);
        $wp->eventID = $version->getEvent()->getID();

        return $wp;
    }

    public function loadDetails()
    {
        $db = \Database::connection();
        $row = $db->GetRow('select eventID from CalendarEventWorkflowProgress where wpID = ?', array($this->wpID));
        $this->setPropertiesFromArray($row);
    }

    public function delete()
    {
        parent::delete();
        $db = \Database::connection();
        $db->Execute('delete from CalendarEventWorkflowProgress where wpID = ?', array($this->wpID));
    }

    /*

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
    */

    public function getWorkflowProgressFormAction()
    {
        $url = app(ResolverManagerInterface::class)->resolve(['/ccm/system/workflow/categories/calendar_event/save_progress']);
        $token = app(Token::class);
        $query = $url->getQuery();
        $query->modify([
            'cID' => $this->eventID,
            'wpID' => $this->getWorkflowProgressID(),
            $token::DEFAULT_TOKEN_NAME => $token->generate('save_workflow_progress')
        ]);
        
        return (string) $url->setQuery($query);
    }

    public function getPendingWorkflowProgressList()
    {
        $list = new ProgressList();
        $list->filter('wpApproved', 0);
        $list->sortBy('wpDateLastAction', 'desc');

        return $list;
    }
}
