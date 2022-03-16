<?php

namespace Concrete\Block\CalendarEvent;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventOccurrence;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\SeoCanonical;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int
     */
    protected $btInterfaceWidth = 550;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 400;

    /**
     * @var string
     */
    protected $btTable = 'btCalendarEvent';

    /**
     * @var string
     */
    protected $mode = 'S';

    /**
     * @var string
     */
    protected $calendarEventAttributeKeyHandle;

    /**
     * @var int|null
     */
    protected $eventID;

    /**
     * {@inheritdoc}
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::CALENDAR,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeDescription()
    {
        return t('Displays a calendar event on a page.');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeName()
    {
        return t('Calendar Event');
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->edit();
    }

    /**
     * @return void
     */
    public function edit()
    {
        /** @var \Concrete\Core\Entity\Attribute\Key\EventKey[] $eventKeys */
        /** @phpstan-ignore-next-line */
        $eventKeys = EventKey::getList();
        $calendars = ['' => t('** Choose a Calendar')];
        $calendarEventPageKeys = ['' => t('** Choose an Event')];
        /** @phpstan-ignore-next-line */
        foreach (CollectionKey::getList() as $ak) {
            if ($ak->getAttributeTypeHandle() == 'calendar_event') {
                $calendarEventPageKeys[$ak->getAttributeKeyHandle()] = $ak->getAttributeKeyDisplayName();
            }
        }
        /** @phpstan-ignore-next-line */
        foreach (Calendar::getList() as $calendar) {
            $calendars[$calendar->getID()] = $calendar->getName();
        }

        $displayEventAttributes = [];
        if (isset($this->displayEventAttributes)) {
            $displayEventAttributes = json_decode($this->displayEventAttributes);
        }

        $this->set('calendarEventPageKeys', $calendarEventPageKeys);
        $this->set('eventKeys', $eventKeys);
        $this->set('calendars', $calendars);
        $this->set('displayEventAttributes', $displayEventAttributes);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return void
     */
    public function save($data)
    {
        $data['calendarID'] = isset($data['calendarID']) ? (int) ($data['calendarID']) : 0;
        $data['eventID'] = isset($data['eventID']) ? (int) ($data['eventID']) : 0;

        $data['allowExport'] = isset($data['allowExport']) && $data['allowExport'] ? 1 : 0;
        $data['displayEventName'] = isset($data['displayEventName']) && $data['displayEventName'] ? 1 : 0;
        $data['displayEventDate'] = isset($data['displayEventDate']) && $data['displayEventDate'] ? 1 : 0;
        $data['displayEventDescription'] = isset($data['displayEventDescription']) && $data['displayEventDescription'] ? 1 : 0;
        $data['enableLinkToPage'] = isset($data['enableLinkToPage']) && $data['enableLinkToPage'] ? 1 : 0;

        $attributes = [];
        if (isset($data['displayEventAttributes']) && is_array($data['displayEventAttributes'])) {
            $attributes = $data['displayEventAttributes'];
        }
        $data['displayEventAttributes'] = json_encode($attributes);
        parent::save($data);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return CalendarEventVersionOccurrence|null
     */
    public function getOccurrence()
    {
        $event = $this->getEvent();
        if (is_object($event)) {
            if ($this->request->query->has('occurrenceID')) {
                /** @var CalendarEventVersionOccurrence|null $occurrence */
                /** @phpstan-ignore-next-line */
                $occurrence = EventOccurrence::getByID($this->request->query->get('occurrenceID'));
                if ($occurrence) {
                    if ($occurrence->getEvent() && $occurrence->getEvent()->getID() == $event->getID()) {
                        /** @phpstan-ignore-next-line */
                        if ($event->isApproved()) {
                            return $occurrence;
                        }
                    }

                    /** @phpstan-ignore-next-line */
                    if ($occurrence->getEvent() && Event::isRelatedTo($occurrence->getEvent(), $event)) {
                        // this is a temporary hack. We need to ultimately make this make sure that
                        // the event matches –but sometimes our events don't match because we imported them as series
                        // events and not as  occurrences of one event.
                        // @TODO delete this code when we no longer need it and all related events are migrated into multidate events.
                        /** @phpstan-ignore-next-line */
                        if ($event->isApproved()) {
                            return $occurrence;
                        }
                    }
                }
            }
            $date = $this->app->make('date')->date('Y-m-d', null, 'app');
            $time = $this->app->make('date')->toDateTime($date . ' 00:00:00', 'GMT', 'app')->getTimestamp();
            $list = $event->getOccurrenceList();
            $list->filterByStartTimeAfter($time);
            $list->setItemsPerPage(1);
            $results = $list->getResults();
            if (!$results) {
                $list = $event->getOccurrenceList();
                $list->sortBy('startTime', 'desc');
                $results = $list->getResults();
            }

            return $results[0];
        }
        if ($this->request->query->has('occurrenceID') && $this->mode == 'R') {
            // request mode
            /** @phpstan-ignore-next-line */
            $occurrence = EventOccurrence::getByID($this->request->query->get('occurrenceID'));
            if ($occurrence) {
                $event = $occurrence->getEvent();
                if ($event && $event->isApproved()) {
                    return $occurrence;
                }
            }
        }

        return null;
    }

    public function on_start()
    {
        if ($this->request->query->has('occurrenceID') && $this->mode == 'R') {
            /** @var SeoCanonical $seo */
            $seo = $this->app->make(SeoCanonical::class);
            $seo->addIncludedQuerystringParameter('occurrenceID');
        }
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $this->set('event', $this->getEvent());

        $displayEventAttributes = [];
        if (isset($this->displayEventAttributes)) {
            $displayEventAttributes = json_decode($this->displayEventAttributes);
        }

        $this->set('displayEventAttributes', $displayEventAttributes);
        $occurrence = $this->getOccurrence();
        $this->set('occurrence', $occurrence);
        $provider = $this->app->make(CalendarServiceProvider::class);
        if ($occurrence) {
            $this->set('event', $occurrence->getEvent());
            $linkFormatter = $provider->getLinkFormatter();
            $eventOccurrenceLink = $linkFormatter->getEventOccurrenceFrontendViewLink($occurrence);
            $this->set('eventOccurrenceLink', $eventOccurrenceLink);
        }
        $formatter = $provider->getDateFormatter();
        $this->set('formatter', $formatter);
    }

    /**
     * @return CalendarEvent|null
     */
    protected function getEvent()
    {
        $event = null;
        if ($this->mode == 'P') {
            $page = Page::getCurrentPage();
            $event = $page->getAttribute($this->calendarEventAttributeKeyHandle);
        } else {
            if ($this->eventID) {
                /** @phpstan-ignore-next-line */
                $event = Event::getByID($this->eventID);
            }
        }

        return $event;
    }
}
