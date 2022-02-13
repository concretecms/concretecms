<?php

namespace Concrete\Block\EventList;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Utility\Service\Validation\Numbers;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int
     */
    protected $btInterfaceWidth = 500;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 340;

    /**
     * @var string
     */
    protected $btTable = 'btEventList';

    /**
     * @var string|null
     */
    protected $calendarAttributeKeyHandle;

    /**
     * @var string|int|null
     */
    protected $caID;

    /**
     * @var Calendar\CalendarService|null
     */
    protected $calendarService;

    /**
     * @var int|null
     */
    protected $totalToRetrieve;

    /**
     * @var int|null
     */
    protected $filterByTopicAttributeKeyID;

    /**
     * @var string|null
     */
    protected $filterByPageTopicAttributeKeyHandle;

    /**
     * @var int|null
     */
    protected $linkToPage;

    /**
     * @var int|null
     */
    protected $filterByFeatured;

    /**
     * @var int|null
     */
    protected $filterByTopicID;

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::CALENDAR,
        ];
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Displays a list of events from a calendar.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Event List');
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->edit();
        $this->set('buttonLinkText', t('View Full Calendar'));
        $this->set('eventListTitle', t('Featured Events'));
        $this->set('totalToRetrieve', 9);
        $this->set('totalPerPage', 3);
        $this->set('filterByTopic', 'none');
        $this->set('titleFormat', 'h5');
    }

    /**
     * @throws \Concrete\Core\Attribute\Exception\InvalidAttributeException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        if (!$this->totalToRetrieve) {
            $this->set('totalToRetrieve', 9);
        }
        $this->requireAsset('font-awesome');
        $list = new EventOccurrenceList();
        $calendar = $this->getCalendarOrCalendars();
        if (is_object($calendar)) {
            $permissions = new Checker($calendar);
            /** @phpstan-ignore-next-line */
            $this->set('canViewCalendar', $permissions->canViewCalendar());
        } elseif (is_array($calendar)) {
            $canViewCalendar = true;
            foreach($calendar as $c) {
                $permissions = new Checker($c);
                /** @phpstan-ignore-next-line */
                if (!$permissions->canViewCalendar()) {
                    $canViewCalendar = false;
                }
            }
            $this->set('canViewCalendar', $canViewCalendar);
        }
        if ($calendar) {
            $date = $this->app->make('date')->date('Y-m-d');
            $time = $this->app->make('date')->toDateTime($date . ' 00:00:00')->getTimestamp();
            $list->filterByEndTimeAfter($time);
            $list->filterByCalendar($calendar);
            if ($this->filterByFeatured) {
                $list->filterByAttribute('is_featured', true);
            }
            if ($this->filterByTopicAttributeKeyID) {
                $ak = EventKey::getByID($this->filterByTopicAttributeKeyID);
                if (is_object($ak)) {
                    $list->filterByAttribute($ak->getAttributeKeyHandle(), $this->filterByTopicID);
                }
            } elseif ($this->filterByPageTopicAttributeKeyHandle) {
                $c = Page::getCurrentPage();
                if (is_object($c)) {
                    $topic = $c->getAttribute($this->filterByPageTopicAttributeKeyHandle);
                    if (!empty($topic[0]) && is_object($topic[0])) {
                        $list->filterByTopic($topic[0]->getTreeNodeID());
                    }
                }
            }

            $this->set('list', $list);
            $this->set('calendar', $calendar);
            if (!empty($this->internalLinkCID)) {
                $calendarPage = Page::getByID($this->internalLinkCID);
                if (is_object($calendarPage) && !$calendarPage->isError()) {
                    $this->set('calendarPage', $calendarPage);
                }
            }
            if ($this->linkToPage) {
                $this->set('linkToPage', Page::getByID($this->linkToPage));
            } else {
                $this->set('linkToPage', null);
            }
            $this->loadKeys();
        }
        $formatter = $this->app->make(CalendarServiceProvider::class)->getDateFormatter();
        $linkFormatter = $this->app->make(CalendarServiceProvider::class)->getLinkFormatter();
        $this->set('formatter', $formatter);
        $this->set('linkFormatter', $linkFormatter);
    }

    /**
     * @param \SimpleXMLElement $blockNode
     *
     * @return void
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        parent::export($blockNode);
        $data = $blockNode->data->record;

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
                $data->addChild('filterByTopic', $node->getTreeNodeDisplayPath());
            }
        }
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function edit()
    {
        $calendars = array_filter($this->getCalendarService()->getList(), static function ($calendar) {
            $p = new Checker($calendar);
            /** @phpstan-ignore-next-line */
            return $p->canViewCalendarInEditInterface();
        });
        $calendarSelect = ['' => t('** Select a Calendar')];
        foreach ($calendars as $calendar) {
            $calendarSelect[$calendar->getID()] = $calendar->getName();
        }
        /** @phpstan-ignore-next-line */
        $keys = CollectionKey::getList();
        $pageAttributeKeys = [];
        foreach ($keys as $ak) {
            if ($ak->getAttributeTypeHandle() === 'topics') {
                $pageAttributeKeys[] = $ak;
            }
        }
        $this->set('pageAttributeKeys', $pageAttributeKeys);
        $this->set('calendars', $calendarSelect);
        $this->set('featuredAttribute', EventKey::getByHandle('is_featured'));
        $this->set('pageSelector', $this->app->make('helper/form/page_selector'));

        $number = new Numbers();
        if (isset($this->caID)) {
            if ($number->integer($this->caID)) {
                $this->set('caID', [$this->caID]); // legacy single calendar field.
            } else {
                $this->set('caID', json_decode($this->caID));
            }
        }
        $this->loadKeys();

        if (!empty($this->filterByPageTopicAttributeKeyHandle)) {
            $this->set('filterByTopic', 'page_attribute');
        } elseif (!empty($this->filterByTopicAttributeKeyID)) {
            $this->set('filterByTopic', 'specific');
        } else {
            $this->set('filterByTopic', 'none');
        }
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return void
     */
    public function save($args)
    {
        if ($args['chooseCalendar'] === 'specific') {
            $args['caID'] = json_encode($args['caID']);
            $args['calendarAttributeKeyHandle'] = '';
        }
        if ($args['chooseCalendar'] === 'site') {
            $args['caID'] = 0;
            // pass through the attribute key handle to save.
        }

        if ($args['filterByTopic'] === 'none') {
            $args['filterByTopicID'] = 0;
            $args['filterByTopicAttributeKeyID'] = 0;
            $args['filterByPageTopicAttributeKeyHandle'] = '';
        }
        if ($args['filterByTopic'] === 'specific') {
            $args['filterByTopicID'] = (int) $args['filterByTopicID'];
            $args['filterByTopicAttributeKeyID'] = (int) $args['filterByTopicAttributeKeyID'];
            $args['filterByPageTopicAttributeKeyHandle'] = '';
        }
        if ($args['filterByTopic'] === 'page_attribute') {
            $args['filterByTopicID'] = 0;
            $args['filterByTopicAttributeKeyID'] = 0;
            // pass through the filterByPageTopicAttributeKeyHandle
        }

        $args['linkToPage'] = (int) $args['linkToPage'];
        $args['filterByFeatured'] = (int) ($args['filterByFeatured'] ?? null);
        parent::save($args);
    }

    protected function getCalendarService(): Calendar\CalendarService
    {
        if (!$this->calendarService) {
            $this->calendarService = $this->app->make(Calendar\CalendarService::class);
        }

        return  $this->calendarService;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Concrete\Core\Entity\Calendar\Calendar|\Concrete\Core\Entity\Calendar\Calendar[]|null
     */
    protected function getCalendarOrCalendars()
    {
        if ($this->calendarAttributeKeyHandle) {
            $site = $this->app->make('site')->getSite();
            $calendar = $site->getAttribute($this->calendarAttributeKeyHandle);
            if (is_object($calendar)) {
                return $calendar;
            }
        }
        if ($this->caID) {
            $number = new Numbers();
            if ($number->integer($this->caID)) {
                return $this->getCalendarService()->getByID($this->caID);
            }

            $caIDs = json_decode($this->caID);
            if (is_array($caIDs)) {
                $calendars = [];
                foreach($caIDs as $caID) {
                    $calendars[] = $this->getCalendarService()->getByID($caID);
                }
                if (count($calendars) === 1) {
                    return $calendars[0];
                }

                return $calendars;
            }
        }

        return null;
    }

    /*
    public function validate($args)
    {
        $calendar = null;
        if ($args['caID']) {
            $calendar = $this->getCalendarService()->getByID($args['caID']);
        } else if ($args['calendarAttributeKeyHandle']) {
            $site = \$this->app->make('site')->getSite();
            $calendar = $site->getAttribute($args['calendarAttributeKeyHandle']);
        }

        $e = \$this->app->make('error');
        if (!is_object($calendar)) {
            $e->add(t('You must choose a valid calendar.'));
        }
        $p = new Checker($calendar);
        if (!$p->canViewCalendarInEditInterface()) {
            $e->add(t('You do not have access to select this calendar.'));
        }
        return $e;
    }*/

    /**
     * @return void
     */
    protected function loadKeys()
    {
        /** @phpstan-ignore-next-line */
        $keys = EventKey::getList();
        $this->set('attributeKeys', array_filter($keys, static function ($ak) {
            return $ak->getAttributeTypeHandle() === 'topics';
        }));
    }
}
