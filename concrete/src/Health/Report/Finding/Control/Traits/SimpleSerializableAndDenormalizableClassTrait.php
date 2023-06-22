<?php
namespace Concrete\Core\Health\Report\Finding\Control\Traits;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * A simple trait to use when you're working with a class that's only property to serialize is "class" and it has nothing
 * to set in the denormlize method.
 */
trait SimpleSerializableAndDenormalizableClassTrait
{
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
        ];
        return $data;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        // nothing here
    }


}
