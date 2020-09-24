<?php
namespace Concrete\Core\Board\Instance\Item\Filterer;

use Concrete\Core\Calendar\Event\EventOccurrenceService;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\InstanceItem;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventFilterer implements FiltererInterface
{

    /**
     * @var EventOccurrenceService
     */
    protected $eventOccurrenceService;

    public function __construct(EventOccurrenceService $eventOccurrenceService)
    {
        $this->eventOccurrenceService = $eventOccurrenceService;
    }

    /**
     * @param CalendarEventConfiguration $configuration
     * @return bool
     */
    public function configurationSupportsFiltering(Configuration $configuration): bool
    {
        if ($configuration->getMaxOccurrencesOfSameEvent() > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param CalendarEventConfiguration $configuration
     * @param InstanceItem[] $items
     * @return bool
     */
    public function filterItems(Configuration $configuration, array $items): array
    {
        $eventAppearances = [];
        $return = [];
        foreach($items as $instanceItem) {
            $item = $instanceItem->getItem();
            if ($item->getDataSource()->getId() == $configuration->getDataSource()->getDataSource()->getId()) {
                $eventOccurrenceID = $item->getUniqueItemId();
                $occurrence = $this->eventOccurrenceService->getByID($eventOccurrenceID);
                if ($occurrence) {
                    $event = $occurrence->getEvent();
                    if ($event) {
                        // Let's track this event in the $eventAppearances array.
                        if (!empty($eventAppearances[$event->getID()])) {
                            $eventAppearances[$event->getID()]++;
                        } else {
                            $eventAppearances[$event->getID()] = 1;
                        }

                        // Now that we've incremented the value, let's check the array to see if we should include
                        // it in the items
                        if ($eventAppearances[$event->getID()] <= $configuration->getMaxOccurrencesOfSameEvent()) {
                            $return[] = $instanceItem;
                        }
                    }

                }
            } else {
                $return[] = $instanceItem; // Make sure to add it back otherwise you'll filter out all unrelated items
            }
        }
        return $return;

    }


}
