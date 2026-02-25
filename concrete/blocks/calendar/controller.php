<?php

namespace Concrete\Block\Calendar;

use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Html\Object\HeadLink;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Utility\Service\Xml;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController implements UsesFeatureInterface
{
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

    /**
     * @var int
     */
    protected $btInterfaceWidth = 500;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 475;

    /**
     * @var string
     */
    protected $btTable = 'btCalendar';

    /**
     * @var \Concrete\Core\Entity\Attribute\Key\EventKey[]
     */
    protected $eventAttributes;

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeDescription()
    {
        return t('Displays a month view calendar on a page.');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeName()
    {
        return t('Calendar');
    }

    /**
     * @return void
     */
    public function on_start()
    {
        /** @phpstan-ignore-next-line  */
        $this->eventAttributes = EventKey::getList();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::CALENDAR,
            Features::IMAGERY,
        ];
    }

    /**
     * @return void
     */
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
        /** @phpstan-ignore-next-line  */
        $keys = EventKey::getList(['atHandle' => 'topics']);
        $this->set('attributeKeys', array_filter($keys, static function ($ak) {
            return $ak->getAttributeTypeHandle() === 'topics';
        }));
    }

    /**
     * @param int $bID
     *
     * @throws \Concrete\Core\Attribute\Exception\InvalidAttributeException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return JsonResponse|void
     */
    public function action_get_events($bID)
    {
        $service = $this->app->make('date');

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
            $list->filterByEndTimeAfter(strtotime($start));
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

            return new JsonResponse($data);
        }
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->loadData();
        $this->set('lightboxPropertiesSelected', []);
        $this->edit();
        // set default view types: month, week, day
        $this->set('viewTypesSelected', ['month', 'basicWeek', 'basicDay']);
        $this->set('viewTypesOrder', ['month_' . t('Month'), 'basicWeek_' . t('Week'), 'basicDay_' . t('Day')]);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Concrete\Core\Entity\Calendar\Calendar|null
     */
    public function getCalendar()
    {
        if ($this->calendarAttributeKeyHandle) {
            $site = $this->app->make('site')->getSite();
            $calendar = $site->getAttribute($this->calendarAttributeKeyHandle);
            if (is_object($calendar)) {
                return $calendar;
            }
        }
        if ($this->caID) {
            /** @phpstan-ignore-next-line */
            return Calendar::getByID($this->caID);
        }

        return null;
    }

    /**
     * @return array<string,string>
     */
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
     * @param string[] $viewTypesOrder
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
            $i++;
        }

        return implode(',', $viewTypeArray);
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
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

        return null;
    }

    /**
     * @param string $key
     * @param \Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence $occurrence
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string
     */
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

        return '';
    }

    /**
     * @return void
     */
    public function composer()
    {
        $this->edit();
    }

    /**
     * @return void
     */
    public function edit()
    {
        $this->loadData();
        $this->set('viewTypesSelected', (array) json_decode($this->viewTypes));
        $this->set('viewTypesOrder', (array) json_decode($this->viewTypesOrder));
        $this->set('lightboxPropertiesSelected', $this->getSelectedLightboxProperties());
        /** @phpstan-ignore-next-line  */
        $calendars = array_filter(Calendar::getList(), function ($calendar) {
            $p = new Checker($calendar);
            /** @phpstan-ignore-next-line */
            return $p->canViewCalendarInEditInterface();
        });
        $calendarSelect = ['' => t('** Select a Calendar')];
        foreach ($calendars as $calendar) {
            $calendarSelect[$calendar->getID()] = $calendar->getName();
        }
        $this->set('calendars', $calendarSelect);
    }

    /**
     * @return bool
     */
    public function supportsLightbox()
    {
        $props = $this->getSelectedLightboxProperties();

        return count($props) > 0;
    }

    /**
     * @param mixed $args
     *
     * @return void
     */
    public function save($args)
    {
        if (($args['_fromCIF'] ?? null) === true) {
            parent::save($args);
            return;
        }
        if ($args['chooseCalendar'] === 'specific') {
            $args['caID'] = (int) $args['caID'];
            $args['calendarAttributeKeyHandle'] = '';
        }
        if ($args['chooseCalendar'] === 'site') {
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

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $this->loadData();
        $calendar = $this->getCalendar();
        if (is_object($calendar)) {
            $permissions = new Checker($calendar);
            /** @phpstan-ignore-next-line */
            if ($permissions->canAccessCalendarRssFeed()) {
                $link = new HeadLink(Url::route(['/feed', 'calendar'], $this->getCalendar()->getID()), 'alternate', 'application/rss+xml');
                $this->addHeaderItem($link);
            }
            $this->set('permissions', $permissions);
            $this->set('calendar', $calendar);
            $this->set('viewTypeString', $this->getViewTypeString((array) json_decode($this->viewTypesOrder)));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(SimpleXMLElement $blockNode)
    {
        $xml = $this->app->make(Xml::class);
        parent::export($blockNode);
        $recordNode = $blockNode->xpath('./data[@table="btCalendar"]/record')[0];

        $caName = '';
        if ($this->caID) {
            $calendar = $this->getCalendar();
            if ($calendar !== null) {
                $caName = $calendar->getName();
            }
        }
        unset($recordNode->caID);
        $xml->createChildElement($recordNode, 'caName', $caName);

        $filterByTopicAttributeKeyHandle = '';
        $filterByTopicPath = '';
        $ak = $this->filterByTopicAttributeKeyID ? EventKey::getByID($this->filterByTopicAttributeKeyID) : null;
        if ($ak) {
            $filterByTopicAttributeKeyHandle = $ak->getAttributeKeyHandle();
            $node = $this->filterByTopicID ? TreeNode::getByID($this->filterByTopicID) : null;
            if ($node) {
                $filterByTopicPath = $node->getTreeNodeDisplayPath();
            }
        }
        unset($recordNode->filterByTopicAttributeKeyID);
        $xml->createChildElement($recordNode, 'filterByTopicAttributeKeyHandle', $filterByTopicAttributeKeyHandle);
        unset($recordNode->filterByTopicID);
        $xml->createChildElement($recordNode, 'filterByTopicPath', $filterByTopicPath);

        $lightboxProperties = [];
        $m = null;
        foreach ($this->getSelectedLightboxProperties() as $prop) {
            if (preg_match('/^ak_(?<id>[1-9]\d*)$/', (string) $prop, $m)) {
                $ak = EventKey::getByID((int) $m['id']);
                if (!$ak) {
                    continue;
                }
                $prop = 'ak_' . $ak->getAttributeKeyHandle();
            }
            $lightboxProperties[] = $prop;
        }
        unset($recordNode->lightboxProperties);
        $xml->createChildElement($recordNode, 'lightboxProperties', json_encode($lightboxProperties));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        $data = parent::getImportData($blockNode, $page);
        $data['_fromCIF'] = true;
        $data['caID'] = 0;
        if (is_string($caName = $data['caName'] ?? null) && ($caName = trim($caName)) !== '') {
            $calendar = Calendar::getByName($caName);
            if ($calendar) {
                $data['caID'] = $calendar->getID();
            }
        }
        $data['filterByTopicAttributeKeyID'] = 0;
        $data['filterByTopicID'] = 0;
        if (is_string($akHandle = $data['filterByTopicAttributeKeyHandle'] ?? null) && ($akHandle = trim($akHandle)) !== '') {
            $ak = EventKey::getByHandle($akHandle);
            if ($ak) {
                $data['filterByTopicAttributeKeyID'] = $ak->getAttributeKeyID();
                if (is_string($filterByTopicPath = $data['filterByTopicPath'] ?? null) && ($filterByTopicPath = trim($filterByTopicPath)) !== '') {
                    $c = $ak->getController();
                    if ($c instanceof \Concrete\Attribute\Topics\Controller) {
                        $treeID = $c->getTopicTreeID();
                        $tree = $treeID ? \Concrete\Core\Tree\Tree::getByID($treeID) : null;
                        if ($tree) {
                            $node = $tree->getNodeByDisplayPath($filterByTopicPath);
                            if ($node) {
                                $data['filterByTopicID'] = $node->getTreeNodeID();
                            }
                        }
                    }
                }
            }
        }
        $lightboxProperties = [];
        $props = ($data['lightboxProperties'] ?? null) ? json_decode($data['lightboxProperties'], true) : null;
        if (is_array($props)) {
            $m = null;
            foreach ($props as $prop) {
                if (preg_match('/^ak_(?<handle>\w+)$/', (string) $prop, $m)) {
                    $ak = EventKey::getByHandle($m['handle']);
                    if (!$ak) {
                        continue;
                    }
                    $prop = 'ak_' . $ak->getAttributeKeyID();
                }
                $lightboxProperties[] = $prop;
            }
        }
        $data['lightboxProperties'] = json_encode($lightboxProperties);
        foreach ([
            'viewTypes',
            'viewTypesOrder',
        ] as $field) {
            $value = ($data[$field] ?? null) ? json_decode($data[$field], true) : [];
            $data[$field] = json_encode(is_array($value) ? $value : []);
        }
        foreach ([
            'navLinks',
            'eventLimit',
        ] as $field) {
            $data[$field] = ($data[$field] ?? false) ? 1 : 0;
        }

        return $data;
    }
}
