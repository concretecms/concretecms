<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\UserInterface\Icon\BasicIconFormatter;
use Concrete\Core\Application\UserInterface\Icon\IconFormatterInterface;
use Concrete\Core\Board\DataSource\Saver\CalendarEventSaver;
use Concrete\Core\Board\DataSource\Saver\SaverInterface;
use Concrete\Core\Board\Item\Populator\CalendarEventPopulator;
use Concrete\Core\Board\Item\Populator\PopulatorInterface;
use Concrete\Core\Filesystem\Element;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventDriver extends AbstractDriver
{
    
    public function getIconFormatter(): IconFormatterInterface
    {
        return new BasicIconFormatter('fas fa-calendar');
    }
    
    public function getConfigurationFormElement(): Element
    {
        return new Element('dashboard/boards/configuration/calendar_event');
    }
    
    public function getSaver(): SaverInterface
    {
        return $this->app->make(CalendarEventSaver::class);
    }

    public function getItemPopulator(): PopulatorInterface
    {
        return $this->app->make(CalendarEventPopulator::class);
    }



}
