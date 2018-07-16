<?php
namespace Concrete\Core\Permission\Response;

class CalendarResponse extends Response
{

    public function canCopyCalendarEvents()
    {
        return $this->canAddCalendarEvent() && $this->canEditCalendarEvents();
    }
}
