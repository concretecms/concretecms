<?php

namespace Concrete\Block\Calendar;

use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Html\Object\HeadLink;
use Core;
use Page;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController
{
    public $helpers = ['form'];

    /**
     * @var int|null
     */
    public $caID;

    /**
     * @var string|null
     */
    public $calendarAttributeKeyHandle;

    /**
     * @var int|null
     */
    public $filterByTopicAttributeKeyID;

    /**
     * @var int|null
     */
    public $filterByTopicID;

    /**
     * @var string|null
     */
    public $viewTypes;

    /**
     * @var string|null
     */
    public $viewTypesOrder;

    /**
     * @var string|null
     */
    public $defaultView;

    /**
     * @var int|null
     */
    public $navLinks;

    /**
     * @var int|null
     */
    public $eventLimit;

    /**
     * @var string|null
     */
    public $lightboxProperties;

    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 475;
    protected $btTable = 'btCalendar';

    public function getBlockTypeDescription()
    {
        return t('Displays a month view calendar on a page.');
    }

    public function getBlockTypeName()
    {
        return t('Calendar');
    }

    public function on_start()
    {
        $this->eventAttributes = EventKey::getList();
    }

    public function loadData()
    {
        $viewTypes = [
            'month' => t('Month'),
            'basicWeek' => t('Week'),
            'basicDay' => t('Day'),
            'listYear' => t('List Year'),
            'listMonth' => t('List Month'),
            'listWeek' => t('List Week'),
            'listDay' => t('List Day'),
        ];
        $this->set('viewTypes', $viewTypes);

        $lightboxProperties = [
            'title' => t('Title'),
            'date' => t('Date'),
            'description' => t('Description'),
            'linkToPage' => t('Link to Page'),
        ];
        foreach ($this->eventAttributes as $ak) {
            $lightboxProperties['ak_' . $ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        $this->set('lightboxProperties', $lightboxProperties);

        // topics
        $keys = EventKey::getList(['atHandle' => 'topics']);
        $this->set('attributeKeys', array_filter($keys, function ($ak) {
            return 'topics' == $ak->getAttributeTypeHandle();
        }));
    }

    public function action_get_events($bID)
    {
        $service = \Core::make('date');

        if ($bID == $this->bID) {
            $start = $this->request->query->get('start');
            $end = $this->request->query->get('end');
            $list = new EventOccurrenceList();
            $list->filterByCalendar($this->getCalendar());
            if ($this->filterByTopicAttributeKeyID) {
                $ak = EventKey::getByID($this->filterByTopicAttributeKeyID);
                if (is_object($ak)) {
                    $list->filterByAttribute($ak->getAttributeKeyHandle(), $this->filterByTopicID);
                }
            }
            $list->filterByStartTimeAfter(strtotime($start));
            $list->filterByStartTimeBefore(strtotime($end));
            $results = $list->getResults();

            $data = [];
            $formatter = $this->app->make(CalendarServiceProvider::class)->getLinkFormatter();
            foreach ($results as $occurrence) {
                $event = $occurrence->getEvent();
                $background = $formatter->getEventOccurrenceBackgroundColor($occurrence);
                $text = $formatter->getEventOccurrenceTextColor($occurrence);
                $obj = new \stdClass();
                $obj->id = $occurrence->getID();
                $obj->title = $event->getName();
                $repetition = $occurrence->getRepetition();
                if ($repetition->isStartDateAllDay()) {
                    $obj->allDay = true;
                    $obj->start = $service->formatCustom('Y-m-d', $occurrence->getStart());
                    $obj->end = $service->formatCustom('Y-m-d', $occurrence->getEnd());
                } else {
                    $obj->start = $service->formatCustom('Y-m-d H:i:s', $occurrence->getStart());
                    $obj->end = $service->formatCustom('Y-m-d H:i:s', $occurrence->getEnd());

                }
                $obj->backgroundColor = $background;
                $obj->borderColor = $background;
                $obj->textColor = $text;
                $url = $formatter->getEventOccurrenceFrontendViewLink($occurrence);
                if ($url) {
                    $obj->url = (string) $url;
                }
                $data[] = $obj;
            }
            $js = new JsonResponse($data);

            return $js;
        }
    }

    public function add()
    {
        $this->loadData();
        $this->set('lightboxPropertiesSelected', []);
        $this->edit();
        // set default view types: month, week, day
        $this->set('viewTypesSelected', ['month', 'basicWeek', 'basicDay']);
        $this->set('viewTypesOrder', ['month_' . t('Month'), 'basicWeek_' . t('Week'), 'basicDay_' . t('Day')]);
    }

    public function getCalendar()
    {
        if ($this->calendarAttributeKeyHandle) {
            $site = \Core::make('site')->getSite();
            $calendar = $site->getAttribute($this->calendarAttributeKeyHandle);
            if (is_object($calendar)) {
                return $calendar;
            }
        }
        if ($this->caID) {
            return Calendar::getByID($this->caID);
        }
    }

    public function getSelectedLightboxProperties()
    {
        return (array) json_decode($this->lightboxProperties);
    }

    /**
     * Extract the view type from the $viewTypesOrder array values.
     *
     * Example: month_Month
     * - "month" is the view type
     * - "Month" is the view type display name
     *
     * @param array $viewTypesOrder
     *
     * @return string
     */
    public function getViewTypeString($viewTypesOrder)
    {
        $viewTypeArray = [];
        $i = 0;
        foreach ($viewTypesOrder as $test) {
            $viewType = explode('_', $test);
            $viewTypeArray[$i] = $viewType[0];
            ++$i;
        }
        $viewTypeString = implode(',', $viewTypeArray);

        return $viewTypeString;
    }

    public function getPropertyTitle($key)
    {
        switch ($key) {
            case 'title':
            case 'description':
            case 'date':
            case 'linkToPage':
                return '';
            default:
                $akID = substr($key, 3);
                $ak = EventKey::getByID($akID);
                if (is_object($ak)) {
                    return $ak->getAttributeKeyDisplayName();
                }
                break;
        }
    }

    public function getPropertyValue($key, $occurrence)
    {
        $event = $occurrence->getEvent();
        if (is_object($event)) {
            switch ($key) {
                case 'title':
                    return '<h3>' . h($event->getName()) . '</h3>';
                case 'description':
                    return $event->getDescription();
                case 'date':
                    $formatter = $this->app->make(CalendarServiceProvider::class)->getDateFormatter();

                    $string = $formatter->getOccurrenceDateString($occurrence);

                    return sprintf('<div class="ccm-block-calendar-dialog-event-time">%s</a></div>', $string);
                case 'linkToPage':
                    $formatter = $this->app->make(CalendarServiceProvider::class)->getLinkFormatter();
                    $url = $formatter->getEventOccurrenceFrontendViewLink($occurrence);
                    if ($url) {
                        return t('<div><a href="%s" class="btn btn-primary">View Event</a></div>', (string) $url);
                    }
                    break;
                default:
                    $akID = substr($key, 3);
                    $ak = EventKey::getByID($akID);
                    if (is_object($ak)) {
                        $av = $event->getAttributeValueObject($ak);
                        if (is_object($av)) {
                            return $av->getValue('displaySanitized', 'display');
                        }
                    }
                    break;
            }
        }
    }

    public function composer()
    {
        $this->edit();
    }

    public function edit()
    {
        $this->loadData();
        $this->set('viewTypesSelected', (array) json_decode($this->viewTypes));
        $this->set('viewTypesOrder', (array) json_decode($this->viewTypesOrder));
        $this->set('lightboxPropertiesSelected', $this->getSelectedLightboxProperties());
        $this->requireAsset('core/topics');
        $calendars = array_filter(Calendar::getList(), function ($calendar) {
            $p = new \Permissions($calendar);

            return $p->canViewCalendarInEditInterface();
        });
        $calendarSelect = ['' => t('** Select a Calendar')];
        foreach ($calendars as $calendar) {
            $calendarSelect[$calendar->getID()] = $calendar->getName();
        }
        $this->set('calendars', $calendarSelect);
    }

    public function supportsLightbox()
    {
        $props = $this->getSelectedLightboxProperties();

        return count($props) > 0;
    }

    /*
    public function validate($args)
    {
        $calendar = Calendar::getByID($args['caID']);
        $e = \Core::make('error');
        if (!is_object($calendar)) {
            $e->add(t('You must choose a valid calendar.'));
        }
        $p = new \Permissions($calendar);
        if (!$p->canViewCalendarInEditInterface()) {
            $e->add(t('You do not have access to select this calendar.'));
        }
        return $e;
    }
    */

    public function save($args)
    {
        if ('specific' == $args['chooseCalendar']) {
            $args['caID'] = (int) $args['caID'];
            $args['calendarAttributeKeyHandle'] = '';
        }
        if ('site' == $args['chooseCalendar']) {
            $args['caID'] = 0;
            // pass through the attribute key handle to save.
        }

        $viewTypes = [];
        if (isset($args['viewTypes']) && is_array($args['viewTypes'])) {
            $viewTypes = $args['viewTypes'];
        }
        $args['viewTypes'] = json_encode($viewTypes);

        $viewTypesOrder = [];
        if (isset($args['viewTypesOrder']) && is_array($args['viewTypesOrder'])) {
            $viewTypesOrder = $args['viewTypesOrder'];
        }
        $args['viewTypesOrder'] = json_encode($viewTypesOrder);

        $args['navLinks'] = isset($args['navLinks']) ? 1 : 0;
        $args['eventLimit'] = isset($args['eventLimit']) ? 1 : 0;

        if (!$args['filterByTopicAttributeKeyID']) {
            $args['filterByTopicID'] = 0;
            $args['filterByTopicAttributeKeyID'] = 0;
        }

        $lightboxProperties = [];
        if (isset($args['lightboxProperties']) && is_array($args['lightboxProperties'])) {
            $lightboxProperties = $args['lightboxProperties'];
        }
        $args['lightboxProperties'] = json_encode($lightboxProperties);

        parent::save($args);
    }

    public function view()
    {
        $this->loadData();
        $calendar = $this->getCalendar();
        if (is_object($calendar)) {
            $permissions = new \Permissions($calendar);
            $this->requireAsset('fullcalendar');
            if ($this->supportsLightbox()) {
                $this->requireAsset('core/lightbox');
            }

            if ($permissions->canAccessCalendarRssFeed()) {
                $link = new HeadLink(\URL::route(['/feed', 'calendar'], $this->getCalendar()->getID()), 'alternate', 'application/rss+xml');
                $this->addHeaderItem($link);
            }
            $this->set('permissions', $permissions);
            $this->set('calendar', $calendar);
            $this->set('viewTypeString', $this->getViewTypeString((array) json_decode($this->viewTypesOrder)));
        }
    }
}
