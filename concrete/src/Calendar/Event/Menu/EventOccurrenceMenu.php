<?php
namespace Concrete\Core\Calendar\Event\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Menu;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Calendar\Event\Formatter\LinkFormatterInterface;

class EventOccurrenceMenu extends Menu
{

    public function __construct(CalendarEventVersionOccurrence $occurrence)
    {
        parent::__construct();
        $this->setAttribute('data-event-occurrence', $occurrence->getID());
        $calendar = $occurrence->getEvent()->getCalendar();
        /**
         * @var $linkFormatter LinkFormatterInterface
         */
        $linkFormatter = \Core::make('calendar/event/formatter/link');
        $url = $linkFormatter->getEventOccurrenceFrontendViewLink($occurrence);
        if ($url) {
            $this->addItem(new LinkItem($url, t('Visit Page')));
        }
        $this->addItem(new DialogLinkItem(
            \URL::to('ccm/calendar/dialogs/event/occurrence') . '?occurrenceID=' . $occurrence->getID(), t('Details'),
        t('View Event'), 500, 500));
        $permissions = new \Permissions($calendar);

        if ($permissions->canEditCalendarEvents()) {
            $this->addItem(new DividerItem());
            $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/calendar/dialogs/event/edit') . '?versionOccurrenceID=' . $occurrence->getID(),  t('Edit'),
                t('Edit'), 640, 500
            ));
            if ($permissions->canCopyCalendarEvents()) {
                $year = date('Y', $occurrence->getStart());
                $month = date('m', $occurrence->getStart());
                $this->addItem(new DialogLinkItem(
                    \URL::to('/ccm/calendar/dialogs/event/duplicate') . '?year=' . $year . '&amp;month=' . $month .
                    '&amp;eventID=' . $occurrence->getEvent()->getID(),  t('Duplicate'),
                    t('Duplicate'), 400, 300
                ));
            }

            if ($permissions->canApproveCalendarEvent()) {
                $this->addItem(new DialogLinkItem(
                    \URL::to('/ccm/calendar/dialogs/event/versions') . '?eventID=' . $occurrence->getEvent()->getID(), t('Versions'),
                    t('Versions')
                ));
            }
            if ($occurrence->getRepetition()->repeats()) {
                $this->addItem(new DialogLinkItem(
                    \URL::to('/ccm/calendar/dialogs/event/delete_occurrence') . '?versionOccurrenceID=' . $occurrence->getID(), t('Delete Occurrence'),
                    t('Delete Occurrence')
                ));
            }
            $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/calendar/dialogs/event/delete') . '?eventID=' . $occurrence->getEvent()->getID(), t('Delete Event'),
                t('Delete Event'), 400, 300
            ));

        }


    }

}
