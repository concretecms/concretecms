<?php
namespace Concrete\Block\EventList;

use Concrete\Core\Attribute\Category\EventCategory;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Calendar\CalendarService;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Utility\Service\Xml;
use Concrete\Attribute\Topics\Controller as TopicsController;
use Concrete\Core\Tree\Tree;
use Core;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var string|null
     */
    public $caID;

    /**
     * @var string|null
     */
    public $calendarAttributeKeyHandle;

    /**
     * @var int|string|null
     */
    public $totalToRetrieve;

    /**
     * @var int|string|null
     */
    public $totalPerPage;

    /**
     * @var int|string|null
     */
    public $filterByTopicAttributeKeyID;

    /**
     * @var int|string|null
     */
    public $filterByTopicID;

    /**
     * @var string|null
     */
    public $filterByPageTopicAttributeKeyHandle;

    /**
     * @var bool|int|string|null
     */
    public $filterByFeatured;

    /**
     * @var string|null
     */
    public $eventListTitle;

    /**
     * @var int|string|null
     */
    public $linkToPage;

    /**
     * @var string|null
     */
    public $titleFormat;

    /**
     * @var string|null
     */
    public $eventPeriod;

    /**
     * @var string|null
     */
    public $eventOrder;

    public $helpers = array('form');

    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 340;
    protected $btTable = 'btEventList';
    protected $btExportPageColumns = ['linkToPage'];

    public function getRequiredFeatures(): array
    {
        return [
            Features::CALENDAR
        ];
    }
    public function getBlockTypeDescription()
    {
        return t("Displays a list of events from a calendar.");
    }

    public function getBlockTypeName()
    {
        return t("Event List");
    }

    public function add()
    {
        $this->edit();
        $this->set('buttonLinkText', t('View Full Calendar'));
        $this->set('eventListTitle', t('Featured Events'));
        $this->set('totalToRetrieve', 9);
        $this->set('totalPerPage', 3);
        $this->set('filterByTopic', 'none');
        $this->set('titleFormat', 'h5');
        $this->set('filterByTopicAttributeKeyID', null);
        $this->set('eventPeriod', 'future_events');
        $this->set('eventOrder', 'most_recent_first');
    }

    protected function getCalendarOrCalendars()
    {
        if ($this->calendarAttributeKeyHandle) {
            $site = \Core::make('site')->getSite();
            $calendar = $site->getAttribute($this->calendarAttributeKeyHandle);
            if (is_object($calendar)) {
                return $calendar;
            }
        }
        if ($this->caID) {
            $number = new Numbers();
            if ($number->integer($this->caID)) {
                return Calendar::getByID($this->caID);
            } else {
                $caIDs = json_decode($this->caID);
                if (is_array($caIDs)) {
                    $calendars = array();
                    foreach($caIDs as $caID) {
                        $calendars[] = Calendar::getByID($caID);
                    }
                    if (count($calendars) == 1) {
                        return $calendars[0];
                    } else {
                        return $calendars;
                    }
                }
            }
        }
    }

    public function view()
    {
        if (!$this->totalToRetrieve) {
            $this->set('totalToRetrieve', 9);
        }
        $this->requireAsset('font-awesome');
        $list = new EventOccurrenceList();
        $calendar = $this->getCalendarOrCalendars();
        if (is_object($calendar)) {
            $permissions = new \Permissions($calendar);
            $this->set('canViewCalendar', $permissions->canViewCalendar());
        } else if (is_array($calendar)) {
            $canViewCalendar = true;
            foreach($calendar as $c) {
                $permissions = new \Permissions($c);
                if (!$permissions->canViewCalendar()) {
                    $canViewCalendar = false;
                }
            }
            $this->set('canViewCalendar', $canViewCalendar);
        }
        if ($calendar) {
            $date = Core::make('date')->date('Y-m-d');
            $time = Core::make('date')->toDateTime($date . ' 00:00:00')->getTimestamp();
            if ($this->eventPeriod == 'past_events') {
              $list->filterByEndTimeBefore($time);
            } elseif ($this->eventPeriod == 'future_events') {
              $list->filterByEndTimeAfter($time);
            }
            //$list->filterByEndTimeAfter($time);
            $list->filterByCalendar($calendar);
            if ($this->filterByFeatured) {
                if ($this->checkSearchableEventAttributeKey('is_featured') === '') {
                    $list->filterByAttribute('is_featured', true);
                }
            }
            if ($this->filterByTopicAttributeKeyID) {
                $ak = EventKey::getByID($this->filterByTopicAttributeKeyID);
                if (is_object($ak)) {
                    if (isset($this->filterByTopicID) && !empty($this->filterByTopicID)) {
                        $list->filterByAttribute($ak->getAttributeKeyHandle(), $this->filterByTopicID);
                    }
                }
            } elseif ($this->filterByPageTopicAttributeKeyHandle) {
                $c = \Page::getCurrentPage();
                $topic = $c->getAttribute($this->filterByPageTopicAttributeKeyHandle);
                if (!empty($topic[0]) && is_object($topic[0])) {
                    $list->filterByTopic($topic[0]->getTreeNodeID());
                }
            }

            if ($this->eventOrder == 'most_recent_first') {
              $list->sortBy('eo.startTime', 'desc');
            } elseif ($this->eventOrder == 'oldest_first') {
              $list->sortBy('eo.startTime', 'asc');
            }


            $this->set('list', $list);
            $this->set('calendar', $calendar);
            if (isset($this->internalLinkCID)) {
                $calendarPage = \Page::getByID($this->internalLinkCID);
                if (is_object($calendarPage) && !$calendarPage->isError()) {
                    $this->set('calendarPage', $calendarPage);
                }
            }
            if ($this->linkToPage) {
                $this->set('linkToPage', \Page::getByID($this->linkToPage));
            }
            $this->loadKeys();
        }
        $formatter = $this->app->make(CalendarServiceProvider::class)->getDateFormatter();
        $linkFormatter = $this->app->make(CalendarServiceProvider::class)->getLinkFormatter();
        $this->set('formatter', $formatter);
        $this->set('linkFormatter', $linkFormatter);
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        parent::export($blockNode);
        $data = $blockNode->data[0]->record[0];
        $caID = (string) $data->caID;
        if ($caID) {
            $idList = null;
            if (is_numeric($caID)) {
                $caID = (int) $caID;
                if ($caID) {
                    $idList = [$caID];
                }
            } else {
                $idList = json_decode($caID);
            }
            if (is_array($idList)) {
                $caNames = [];
                $service = $this->app->make(CalendarService::class);
                foreach ($idList as $id) {
                    $calendar = $service->getByID($id);
                    if ($calendar) {
                        $caNames[] = $calendar->getName();
                    }
                }
                unset($data->caID);
                $this->app->make(Xml::class)->createChildElement($data, 'caNames', json_encode($caNames));
            }
        }
        if ($this->filterByTopicAttributeKeyID) {
            $ak = EventKey::getByID($this->filterByTopicAttributeKeyID);
            if (is_object($ak)) {
                unset($data->filterByTopicAttributeKeyID);
                $data->addChild('filterByTopicAttributeKey', $ak->getAttributeKeyHandle());
            }
        }
        if ($this->filterByTopicID) {
            $node = Node::getByID($this->filterByTopicID);
            if (is_object($node)) {
                unset($data->filterByTopicID);
                $data->addChild('filterByTopicPath', $node->getTreeNodeDisplayPath());
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        $data = parent::getImportData($blockNode, $page);
        if (empty($data['chooseCalendar'])) {
            $data['chooseCalendar'] = 'site';
        }
        if (!isset($data['caID'])) {
            $data['caID'] = 0;
            if (!empty($data['caNames'])) {
                $caNames = json_decode($data['caNames']);
                if (is_array($caNames)) {
                    $caIDs = [];
                    $service = $this->app->make(CalendarService::class);
                    foreach ($caNames as $caName) {
                        $calendar = $service->getByName($caName);
                        if ($calendar) {
                            $caIDs[] = $calendar->getID();
                        }
                    }
                    if ($caIDs !== []) {
                        $data['caID'] = $caIDs;
                        $data['chooseCalendar'] = 'specific';
                    }
                }
            }
        }
        if (!isset($data['filterByTopicAttributeKeyID'])) {
            $data['filterByTopicAttributeKeyID'] = 0;
        }
        if (!isset($data['filterByTopicID'])) {
            $data['filterByTopicID'] = 0;
        }
        $filterByTopicAttributeKey = (string) ($data['filterByTopicAttributeKey'] ?? '');
        $filterByTopicPath = (string) ($data['filterByTopicPath'] ?? '');
        if ($filterByTopicAttributeKey !== '' && $filterByTopicPath !== '') {
            $topicAttributeKey = $this->app->make(EventCategory::class)->getAttributeKeyByHandle($filterByTopicAttributeKey);
            $topicController = $topicAttributeKey ? $topicAttributeKey->getController() : null;
            if ($topicController instanceof TopicsController) {
                $tree = Tree::getByID($topicController->getTopicTreeID());
                if ($tree instanceof Tree) {
                    $node = $tree->getNodeByDisplayPath($filterByTopicPath);
                    if ($node) {
                        $data['filterByTopicAttributeKeyID'] = $topicAttributeKey->getAttributeKeyID();
                        $data['filterByTopicID'] = $node->getTreeNodeID();
                    };
                }
            }
        }
        if ((string) ($data['filterByTopic'] ?? '') === '') {
            if (!empty($data['filterByTopicID'])) {
                $data['filterByTopic'] = 'specific';
            } elseif (((string) $data['filterByPageTopicAttributeKeyHandle']) ?? '' !== '') {
                $data['filterByTopic'] = 'page_attribute';
            } else {
                $data['filterByTopic'] = 'none';
            }
        }

        return $data;
    }

    public function edit()
    {
        $calendars = array_filter(Calendar::getList(), function ($calendar) {
            $p = new \Permissions($calendar);

            return $p->canViewCalendarInEditInterface();
        });
        $calendarSelect = array('' => t('** Select a Calendar'));
        foreach ($calendars as $calendar) {
            $calendarSelect[$calendar->getID()] = $calendar->getName();
        }
        $keys = CollectionKey::getList();
        $pageAttributeKeys = array();
        foreach ($keys as $ak) {
            if ($ak->getAttributeTypeHandle() == 'topics') {
                $pageAttributeKeys[] = $ak;
            }
        }
        $this->set('pageAttributeKeys', $pageAttributeKeys);
        $this->set('calendars', $calendarSelect);
        $this->set('featuredAttributeUnusableReason', $this->checkSearchableEventAttributeKey('is_featured'));
        $this->set('pageSelector', Core::make("helper/form/page_selector"));

        $number = new Numbers();
        if (isset($this->caID)) {
            if ($number->integer($this->caID)) {
                $this->set('caID', array($this->caID)); // legacy single calendar field.
            } else {
                $this->set('caID', json_decode($this->caID));
            }
        }
        $this->loadKeys();

        if (isset($this->filterByPageTopicAttributeKeyHandle) && !empty($this->filterByPageTopicAttributeKeyHandle)) {
            $this->set('filterByTopic', 'page_attribute');
        } elseif (isset($this->filterByTopicAttributeKeyID) && !empty($this->filterByTopicAttributeKeyID)) {
            $this->set('filterByTopic', 'specific');
        } else {
            $this->set('filterByTopic', 'none');
        }
    }

    /*
    public function validate($args)
    {
        $calendar = null;
        if ($args['caID']) {
            $calendar = Calendar::getByID($args['caID']);
        } else if ($args['calendarAttributeKeyHandle']) {
            $site = \Core::make('site')->getSite();
            $calendar = $site->getAttribute($args['calendarAttributeKeyHandle']);
        }

        $e = \Core::make('error');
        if (!is_object($calendar)) {
            $e->add(t('You must choose a valid calendar.'));
        }
        $p = new \Permissions($calendar);
        if (!$p->canViewCalendarInEditInterface()) {
            $e->add(t('You do not have access to select this calendar.'));
        }
        return $e;
    }*/

    protected function loadKeys()
    {
        $keys = EventKey::getList(array('atHandle' => 'topics'));
        $this->set('attributeKeys', array_filter($keys, function ($ak) {
            return $ak->getAttributeTypeHandle() == 'topics';
        }));
    }

    public function save($args)
    {
        if ($args['chooseCalendar'] == 'specific') {
            $args['caID'] = json_encode($args['caID']);
            $args['calendarAttributeKeyHandle'] = '';
        }
        if ($args['chooseCalendar'] == 'site') {
            $args['caID'] = 0;
            // pass through the attribute key handle to save.
        }

        if ($args['filterByTopic'] == 'none') {
            $args['filterByTopicID'] = 0;
            $args['filterByTopicAttributeKeyID'] = 0;
            $args['filterByPageTopicAttributeKeyHandle'] = '';
        }
        if ($args['filterByTopic'] == 'specific') {
            $args['filterByTopicID'] = intval($args['filterByTopicID']);
            $args['filterByTopicAttributeKeyID'] = intval($args['filterByTopicAttributeKeyID']);
            $args['filterByPageTopicAttributeKeyHandle'] = '';
        }
        if ($args['filterByTopic'] == 'page_attribute') {
            $args['filterByTopicID'] = 0;
            $args['filterByTopicAttributeKeyID'] = 0;
            // pass through the filterByPageTopicAttributeKeyHandle
        }

        $args['linkToPage'] = intval($args['linkToPage']);
        $args['filterByFeatured'] = intval($args['filterByFeatured'] ?? null);
        parent::save($args);
    }

    /**
     * @return string the reason why the attribute key is not usable (or an empty string if it's usable)
     */
    protected function checkSearchableEventAttributeKey(string $handle): string
    {
        $category = $this->app->make(EventCategory::class);
        $key = $category->getAttributeKeyByHandle($handle);
        if ($key === null) {
            return t('You must create the %s event attribute first.', "<code>{$handle}</code>");
        }
        if (!$key->isAttributeKeyContentIndexed()) {
            return t('The %s event attribute must be indexed.', "<code>{$handle}</code>");
        }

        return '';
    }
}
