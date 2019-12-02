<?php
namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Board\Instance\Slot\Content\SummaryObjectCreatorTrait;
use Concrete\Core\Board\Item\Data\CalendarEventData;
use Concrete\Core\Board\Item\Data\DataInterface;
use Concrete\Core\Board\Item\Data\PageData;
use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventService;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventPopulator extends AbstractPopulator
{

    use SummaryObjectCreatorTrait;

    /**
     * @var EventService 
     */
    protected $eventService;
    
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    
    public function getDataClass(): string
    {
        return CalendarEventData::class;
    }

    /**
     * @param CalendarEventData $data
     * @return ObjectInterface
     */
    public function createContentObject(DataInterface $data): ObjectInterface
    {
        $event = $this->eventService->getByID($data->getEventID());
        if ($event) {
            return $this->createSummaryContentObject($event);            
        }
    }

}
