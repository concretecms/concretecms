<?php
namespace Concrete\Core\Board\Item\Data;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventData implements DataInterface
{

    /**
     * @var int
     */
    protected $eventID = 0;

    public function __construct(CalendarEvent $event = null)
    {
        if ($event) {
            $this->eventID = $event->getID();
        }
    }

    /**
     * @return int
     */
    public function getEventID(): int
    {
        return $this->eventID;
    }

    /**
     * @param int $eventID
     */
    public function setEventID(int $eventID): void
    {
        $this->eventID = $eventID;
    }
    
        public function jsonSerialize()
    {
        return ['eventID' => $this->eventID];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $this->eventID = $data['eventID'];
    }


}
