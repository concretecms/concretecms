<?php

namespace Concrete\Core\Calendar\Event\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\PopoverMenu;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Calendar\Event\Formatter\LinkFormatterInterface;
use Concrete\Core\Support\Facade\Url;

class EventOccurrenceMenu extends PopoverMenu
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
        $this->addItem(new LinkItem(Url::to('/ccm/calendar/event/export')->setQuery(['eventID' => $occurrence->getEvent()->getID()]), t('Export Event')));
        $permissions = new \Permissions($calendar);

        if ($permissions->canEditCalendarEvents()) {
            $this->addItem(new DividerItem());
            $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/calendar/dialogs/event/edit') . '?versionOccurrenceID=' . $occurrence->getID(), t('Edit'),
                t('Edit'), 1100, 600
            ));
            $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/calendar/dialogs/event/summary_templates') . '?versionOccurrenceID=' . $occurrence->getID(), t('Summary Templates'),
                t('Summary Templates'), '90%', '70%'
            ));
            if ($permissions->canCopyCalendarEvents()) {
                $year = date('Y', $occurrence->getStart());
                $month = date('m', $occurrence->getStart());
                $this->addItem(new DialogLinkItem(
                    \URL::to('/ccm/calendar/dialogs/event/duplicate') . '?year=' . $year . '&amp;month=' . $month .
                    '&amp;eventID=' . $occurrence->getEvent()->getID(), t('Duplicate'),
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
