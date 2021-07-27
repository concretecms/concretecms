<?php

namespace Concrete\Core\Notification\Events;

use Concrete\Core\Notification\Events\ServerEvent\EventInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ServerEventNormlizer implements NormalizerInterface
{

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof EventInterface;
    }

    /**
     * @param EventInterface $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|void|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $data = [];
        $data['event'] = $object->getEvent();
        $data['data'] = $object->getData();
        return $data;
    }

}

