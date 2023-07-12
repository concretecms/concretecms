<?php
namespace Concrete\Core\Health\Report\Finding\Control;

use Concrete\Core\Health\Report\Finding\Control\Formatter\ButtonFormatter;
use Concrete\Core\Health\Report\Finding\Control\Formatter\FormatterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ButtonControl implements ControlInterface
{

    /**
     * @var LocationInterface
     */
    protected $location;

    public function __construct(LocationInterface $location = null)
    {
        $this->location = $location;
    }

    /**
     * @param LocationInterface $location
     */
    public function setLocation(?LocationInterface $location): void
    {
        $this->location = $location;
    }

    /**
     * @return LocationInterface
     */
    public function getLocation(): LocationInterface
    {
        return $this->location;
    }

    public function getFormatter(): FormatterInterface
    {
        return new ButtonFormatter();
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [
            'class' => static::class,
            'location' => $this->location,
        ];
        return $data;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $location = $denormalizer->denormalize($data['location'], $data['location']['class']);
        $this->location = $location;
    }



}
