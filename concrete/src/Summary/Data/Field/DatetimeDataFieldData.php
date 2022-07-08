<?php
namespace Concrete\Core\Summary\Data\Field;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use DateTime;
use DateTimeZone;

class DatetimeDataFieldData implements DataFieldDataInterface
{

    /**
     * @var DateTime | null
     */
    protected $dateTime;
    
    public function __construct(DateTime $dateTime = null)
    {
        if ($dateTime) {
            $this->dateTime = $dateTime;
        }
    }

    public function __toString()
    {
        return ($this->dateTime !== null) ? (string) $this->dateTime->getTimestamp() : '';
    }

    /**
     * @return DateTime|null
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {

        if ($this->dateTime !== null) {
            return [
                'class' => self::class,
                'timestamp' => (string) $this->dateTime->getTimestamp(),
                'timezone' => (string) $this->dateTime->getTimezone()->getName()
            ];
        }

        return [
            'class' => self::class,
            'timestamp' => '',
            'timezone' => ''
        ];
    }
    
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        if (isset($data['timestamp'])) {
            $dateTime = new DateTime();
            $dateTime->setTimestamp($data['timestamp']);
            $dateTime->setTimezone(new DateTimeZone($data['timezone']));
            $this->dateTime = $dateTime;
        }
    }
    
    public function __call($name, $arguments)
    {
        return $this->dateTime->$name(...$arguments);
    }
}
