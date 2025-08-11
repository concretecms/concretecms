<?php
namespace Concrete\Core\Summary\Data\Field;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DataFieldData implements DataFieldDataInterface
{

    /**
     * @var int|float|string|bool
     */
    protected $data;
    
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * @param bool|float|int|string $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
    
    public function __toString()
    {
        return (string) $this->data;
    }
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'data' => (string) $this->data
        ];
    }
    
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $this->setData($data['data']);
    }
}
