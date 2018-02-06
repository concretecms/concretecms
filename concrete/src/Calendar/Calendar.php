<?php
namespace Concrete\Core\Calendar;

use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Calendar\Calendar\CalendarService;

class Calendar extends Facade
{

    public static function getFacadeAccessor()
    {
        return CalendarService::class;
    }

}
