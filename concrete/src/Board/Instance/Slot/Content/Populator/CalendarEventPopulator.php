<?php
namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Board\Instance\Item\Data\CalendarEventData;
use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Board\Instance\Logger\LoggerInterface;
use Concrete\Core\Board\Instance\Slot\Content\SummaryObjectCreatorTrait;
use Concrete\Core\Calendar\Event\EventOccurrenceService;
use Concrete\Core\Permission\Checker;

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
     * @param DataInterface $data
     * @param LoggerInterface $logger
     * @param bool $enforceViewPermissions
     * @return array
     */
    public function createContentObjects(DataInterface $data, LoggerInterface $logger, bool $enforceViewPermissions = false): array
    {
        if (!$data instanceof CalendarEventData) {
            throw new \InvalidArgumentException(t('The item data must be an instance of %s.', CalendarEventData::class));
        }
        $occurrence = $this->eventOccurrenceService->getByID($data->getOccurrenceID());
        if ($occurrence) {
            if ($enforceViewPermissions) {
                $event = $occurrence->getEvent();
                $calendar = $event ? $event->getCalendar() : null;
                if (!$calendar) {
                    return [];
                }
                $checker = new Checker($calendar);
                if (!$checker->canViewCalendar()) {
                    return [];
                }
            }
            return $this->createSummaryContentObjects($occurrence, $logger);
        }
        return [];
    }

}
