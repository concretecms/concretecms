<?php
namespace Concrete\Core\Board\Instance\Item\Data;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventData implements DataInterface
{

    /**
     * @var int
     */
    protected $occurrenceID = 0;

    public function __construct(CalendarEventVersionOccurrence $occurrence = null)
    {
        if ($occurrence) {
            $this->occurrenceID = $occurrence->getID();
        }
    }

    /**
     * @return int
     */
    public function getOccurrenceID()
    {
        return $this->occurrenceID;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['occurrenceID' => $this->occurrenceID];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $this->occurrenceID = $data['occurrenceID'];
    }


}
