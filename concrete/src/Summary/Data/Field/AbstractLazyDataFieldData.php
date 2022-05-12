<?php
namespace Concrete\Core\Summary\Data\Field;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


abstract class AbstractLazyDataFieldData implements LazyDataFieldDataInterface
{

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => static::class,
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        // Nothing
    }



}
