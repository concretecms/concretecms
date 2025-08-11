<?php
namespace Concrete\Attribute\Calendar;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\Fractal\Transformer\CalendarTransformer;
use Concrete\Core\Api\Resources;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Utility\Service\Xml;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;

class Controller extends \Concrete\Attribute\Number\Controller implements ApiResourceValueInterface
{
    protected $helpers = ['form'];
    protected $calendar;

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('calendar');
    }

    /**
     * @param $value Calendar
     */
    public function createAttributeValue($value)
    {
        $av = new NumberValue();
        $av->setValue($value->getID());

        return $av;
    }

    public function getSearchIndexValue()
    {
        return '1';
    }

    public function exportValue(\SimpleXMLElement $akv)
    {
        $val = $this->attributeValue->getValue();

        return $this->app->make(Xml::class)->createChildElement($akv, 'value', $val->getName());
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        $calendar = Calendar::getByID($data['calendarID']);
        if (is_object($calendar)) {
            return $this->createAttributeValue($calendar);
        } else {
            $av = new NumberValue();
            $av->setValue(0);
            return $av;
        }
    }

    public function getDisplayValue()
    {
        $caID = $this->getAttributeValue()->getValue();
        if ($caID) {
            $calendar = Calendar::getByID($caID);
            if ($calendar) {
                $url = app('url');
                return sprintf('<a href="%s">%s</a>', $url->to('/dashboard/calendar/events', 'view', $calendar->getID()), $calendar->getName());
            }
        }
    }

    public function getValue()
    {
        $value = $this->getAttributeValue()->getValueObject();
        if ($value) {
            return Calendar::getByID(intval($value->getValue()));
        }
    }

    public function form()
    {
        if (is_object($this->attributeValue)) {
            $calendar = $this->getValue();
            if (is_object($calendar)) {
                $this->set('calendarID', $calendar->getID());
            }
        }

        $calendars = ['' => t('** Choose a Calendar')];
        foreach (Calendar::getList() as $calendar) {
            $calendars[$calendar->getID()] = $calendar->getName();
        }
        $this->set('calendars', $calendars);
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
        if ($calendar = $this->getValue()) {
            return new Item($calendar, new CalendarTransformer(), Resources::RESOURCE_CALENDARS);
        }
        return null;
    }

}
