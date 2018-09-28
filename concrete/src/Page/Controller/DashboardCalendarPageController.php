<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Calendar\Calendar;

class DashboardCalendarPageController extends DashboardSitePageController
{
    public function getCalendars($caID)
    {
        $calendars = array_filter(Calendar::getList($this->site), function ($calendar) {
            $p = new \Permissions($calendar);

            return $p->canViewCalendarInEditInterface();
        });

        if (count($calendars) == 0) {
            $this->redirect('/dashboard/calendar/add');
        }
        $defaultCalendar = $calendars[0];
        if ($caID) {
            $calendar = Calendar::getByID(intval($caID));
            $cp = new \Permissions($calendar);
            if (!$cp->canViewCalendarInEditInterface()) {
                unset($calendar);
            }
        } else {
            $calendar = null;
        }

        if (!$calendar) {
            $calendar = $defaultCalendar;
        }

        $filterTopics = array();

        $ak = $calendar->getCalendarTopicsAttributeKey();
        if (is_object($ak)) {
            $node = Node::getByID($ak->getController()->getTopicParentNode());
            if ($node instanceof Category) {
                $node->populateChildren();
                $filterTopics = $node->getChildNodes();
                $validIDs = array_map(function (Node $node) {
                    return $node->getTreeNodeID();
                }, $filterTopics);
            }
            $this->set('topics', $filterTopics);
        }

        return array($calendar, $calendars);
    }
}
