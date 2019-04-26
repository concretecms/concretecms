<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

class CalendarObject implements ObjectInterface
{

    protected $calendar;

    /**
     * Calendar constructor.
     * @param $calendar \Concrete\Core\Entity\Calendar\Calendar
     */
    public function __construct($calendar)
    {
        $this->calendar = $calendar;
    }

    public function getPermissionObject()
    {
        return $this->calendar;
    }


}
