<?php
namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Board\Instance\Slot\Content\SummaryObjectCreatorTrait;
use Concrete\Core\Board\Instance\Item\Data\CalendarEventData;
use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Calendar\Event\EventOccurrenceService;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventPopulator extends AbstractPopulator
{

    use SummaryObjectCreatorTrait;

    /**
     * @var EventOccurrenceService
     */
    protected $eventOccurrenceService;

    public function __construct(EventOccurrenceService $eventOccurrenceService)
    {
        $this->eventOccurrenceService = $eventOccurrenceService;
    }

    public function getDataClass(): string
    {
        return CalendarEventData::class;
    }

    /**
     * @param CalendarEventData $data
     * @return ObjectInterface[]
     */
    public function createContentObjects(DataInterface $data): array
    {
        $occurrence = $this->eventOccurrenceService->getByID($data->getOccurrenceID());
        if ($occurrence) {
            return $this->createSummaryContentObjects($occurrence);
        }
    }

}
