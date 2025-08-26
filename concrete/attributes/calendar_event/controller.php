<?php
namespace Concrete\Attribute\CalendarEvent;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\Fractal\Transformer\CalendarEventTransformer;
use Concrete\Core\Api\Fractal\Transformer\CalendarTransformer;
use Concrete\Core\Api\Resources;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Calendar\CalendarService;
use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\Formatter\LinkFormatter;
use Concrete\Core\Calendar\Event\Formatter\LinkFormatterInterface;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Utility\Service\Xml;
use Doctrine\ORM\EntityManagerInterface;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use SimpleXMLElement;

class Controller extends \Concrete\Attribute\Number\Controller implements ApiResourceValueInterface
{
    protected $helpers = ['form'];
    protected $calendar;

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('calendar-check');
    }

    /**
     * @param CalendarEvent|null $value
     */
    public function createAttributeValue($value)
    {
        $av = new NumberValue();
        $av->setValue($value ? $value->getID() : null);

        return $av;
    }

    public function getSearchIndexValue()
    {
        $value = $this->getAttributeValue()->getValueObject();
        if ($value) {
            return intval($value->getValue());
        }
    }

    public function getPlainTextValue()
    {
        $value = $this->getValue();
        /**
         * @var $linkFormatter LinkFormatterInterface
         */
        $linkFormatter = \Core::make('calendar/event/formatter/link');
        $event = Event::getByID($value);
        if ($event) {
            $text = $event->getName();
            $url = $linkFormatter->getEventFrontendViewLink($event);
            if ($url) {
                $text .= ' (' . $url . ')';
            }
            return $text;
        }
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        $event = null;
        if (!empty($data['eventID'])) {
            $event = Event::getByID($data['eventID']);
        }
        if ($event) {
            return $this->createAttributeValue($event);
        } else {
            $av = new NumberValue();
            $av->setValue(0);
            return $av;
        }
    }

    /**
     * @return \Concrete\Core\Entity\Calendar\CalendarEvent|null
     */
    public function getValue()
    {
        $value = $this->getAttributeValue()->getValueObject();
        if ($value) {
            return Event::getByID(intval($value->getValue()));
        }
    }

    public function getDisplayValue()
    {
        $event = $this->getValue();
        if ($event) {
            $formatter = app(LinkFormatter::class);
            $link = $formatter->getEventFrontendViewLink($event);
            if ($link) {
                return sprintf('<a href="%s">%s</a>', $link, $event->getName());
            } else {
                return $event->getName();
            }
        }
    }


    public function form()
    {
        $event = null;
        if (is_object($this->attributeValue)) {
            $event = $this->getValue();
        }

        if (!$event) {
            if ($this->request->query->has($this->attributeKey->getAttributeKeyHandle())) {
                $event = Event::getByID(intval($this->request->query->get($this->attributeKey->getAttributeKeyHandle())));
            }
        }

        if ($event) {
            $this->set('eventID', $event->getID());
            $this->set('calendarID', $event->getCalendar()->getID());
            $this->set('event', $event);
        }

        $calendars = ['' => t('** Choose a Calendar')];
        foreach (Calendar::getList() as $calendar) {
            $calendars[$calendar->getID()] = $calendar->getName();
        }
        $this->set('calendars', $calendars);
    }

    public function search()
    {
        $this->form();
        $v = $this->getView();
        $v->render();
    }

    public function searchForm($list)
    {
        $eventID = (int) ($this->request('eventID'));
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $eventID, '=');
        return $list;
    }

    public function createAttributeValueFromNormalizedJson($json)
    {
        $av = new NumberValue();
        if ($json) {
            $av->setValue($json);
        } else {
            $av->setValue(0);
        }
        return $av;
    }

    public function getApiValueResource(): ?ResourceInterface
    {
        if ($event = $this->getValue()) {
            $transformer = new CalendarEventTransformer();
            $transformer->setDefaultIncludes(['custom_attributes', 'version']);
            return new Item($event, $transformer, Resources::RESOURCE_CALENDAR_EVENTS);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::exportValue()
     */
    public function exportValue(SimpleXMLElement $akv)
    {
        $xValue = $akv->addChild('value');
        $event = $this->getValue();
        $eventVersion = $event ? $event->getApprovedVersion() : '';
        $eventOccurrence = $eventVersion ? $eventVersion->getOccurrences()->first() : null;
        $calendar = $event ? $event->getCalendar() : null;
        if (!$eventOccurrence || !$calendar) {
            return;
        }
        $xValue['calendar'] = $calendar->getName();
        $xValue['event'] = $eventVersion->getName();
        $xValue['startTime'] = $eventOccurrence->getOccurrence()->getStart();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::exportValue()
     *
     * @return \Concrete\Core\Entity\Calendar\CalendarEvent|null
     */
    public function importValue(SimpleXMLElement $akv)
    {
        if (($calendarName = trim((string) $akv->value['calendar'])) === '') {
            return null;
        }
        if (($eventName = trim((string) $akv->value['event'])) === '') {
            return null;
        }
        if (($startTime = (int) trim((string) $akv->value['startTime'])) === 0) {
            return null;
        }
        $calendar = $calendarName === '' ? null : $this->app->make(CalendarService::class)->getByName($calendarName);
        if ($calendar === null) {
            return null;
        }
        $qb = $this->app->make(EntityManagerInterface::class)->createQueryBuilder();
        $qb
            ->setParameter('calendarID', $calendar->getID())
            ->setParameter('eventName', $eventName)
            ->setParameter('startTime', $startTime)
            ->select('event')
            ->from(CalendarEvent::class, 'event')
            ->innerJoin('event.versions', 'version')
            ->innerJoin('version.occurrences', 'versionOccurrence')
            ->innerJoin('versionOccurrence.occurrence', 'occurrence')
            ->andWhere('event.calendar = :calendarID')
            ->andWhere('version.evName = :eventName')
            ->andWhere('occurrence.startTime = :startTime')
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->execute()[0] ?? null;
    }
}
