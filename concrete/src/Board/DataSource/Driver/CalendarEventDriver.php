<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\UserInterface\Icon\BasicIconFormatter;
use Concrete\Core\Application\UserInterface\Icon\IconFormatterInterface;
use Concrete\Core\Board\DataSource\Saver\CalendarEventSaver;
use Concrete\Core\Board\DataSource\Saver\SaverInterface;
use Concrete\Core\Board\Instance\Item\Filterer\CalendarEventFilterer;
use Concrete\Core\Board\Instance\Item\Filterer\FiltererInterface as ItemFiltererInterface;
use Concrete\Core\Board\Instance\Slot\Content\Populator\CalendarEventPopulator as CalendarEventContentPopulator;
use Concrete\Core\Board\Instance\Slot\Content\Populator\PopulatorInterface as ContentPopulatorInterface;
use Concrete\Core\Board\Instance\Item\Populator\CalendarEventPopulator as CalendarEventItemPopulator;
use Concrete\Core\Board\Instance\Item\Populator\PopulatorInterface as ItemPopulatorInterface;
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

    public function getItemPopulator(): ItemPopulatorInterface
    {
        return $this->app->make(CalendarEventItemPopulator::class);
    }

    public function getContentPopulator(): ContentPopulatorInterface
    {
        return $this->app->make(CalendarEventContentPopulator::class);
    }

    public function getItemFilterer(): ?ItemFiltererInterface
    {
        return $this->app->make(CalendarEventFilterer::class);
    }
}
