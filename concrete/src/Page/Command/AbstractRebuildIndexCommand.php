<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Attribute\Command\RebuildIndexCommandInterface;
use Concrete\Core\Foundation\Command\HandlerAwareCommandInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractRebuildIndexCommand implements RebuildIndexCommandInterface, HandlerAwareCommandInterface
{

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        return [];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
    }

}
