<?php
namespace Concrete\Core\Permission\Response;

/**
 * @since 8.3.0
 */
class CalendarResponse extends Response
{

    public function canCopyCalendarEvents()
    {
        return $this->canAddCalendarEvent() && $this->canEditCalendarEvents();
    }
}
