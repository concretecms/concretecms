<?php
namespace Concrete\Core\Calendar;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Calendar\Event\Formatter\DateFormatter;
use Concrete\Core\Calendar\Event\Formatter\LinkFormatter;

class CalendarServiceProvider extends Provider
{
    public function register()
    {
        $this->app->bind('calendar/event/occurrence/factory', '\\Concrete\\Core\\Calendar\\Event\\EventOccurrenceFactory');
        $this->app->singleton('calendar/event/formatter/link', LinkFormatter::class);
        $this->app->singleton('calendar/event/formatter/date', DateFormatter::class);
    }

    public function getLinkFormatter()
    {
        return $this->app->make('calendar/event/formatter/link');
    }

    public function getDateFormatter()
    {
        return $this->app->make('calendar/event/formatter/date');
    }

    public function provides()
    {
        return array(
            'calendar/event/occurrence/factory',
            'calendar/event/formatter/link',
            'calendar/event/formatter/date',
        );
    }
}
