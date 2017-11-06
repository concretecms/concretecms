<?php
namespace Concrete\Attribute\Calendar;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;

class Controller extends \Concrete\Attribute\Number\Controller
{
    protected $helpers = ['form'];
    protected $calendar;

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('calendar');
    }

    /**
     * @param $value CalendarEvent
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
        $cnode = $akv->addChild('value');
        $node = dom_import_simplexml($cnode);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDataSection($val->getName()));
        return $cnode;
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
}
