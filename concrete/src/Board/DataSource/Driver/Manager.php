<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    public function createPageDriver()
    {
        return $this->app->make(PageDriver::class);
    }

    public function createCalendarEventDriver()
    {
        return $this->app->make(CalendarEventDriver::class);
    }

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->driver('page');
        $this->driver('calendar_event');
    }

}
