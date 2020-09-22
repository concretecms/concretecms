<?php

namespace Concrete\Core\Foundation\Queue;

use Assert\Assertion;
use Bernard\Normalizer\AbstractAggregateNormalizerAware;
use League\Tactician\Bernard\QueueCommand;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Supersedes the QueueCommandNormalizer in Bernard. This is not ideal. I don't know why they a) marked that class
 * final or b) made it so that data MUST exist. This makes it so you cannot queue a command taht doesn't have some
 * kind of getter/setter/data in it. So this replaces that and simply removes data from the choicesNotEmpty
 * assertion.
 *
 */
final class QueueCommandNormalizer extends AbstractAggregateNormalizerAware implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'class' => get_class($object->getCommand()),
            'name' => $object->getName(),
            'data' => $this->aggregate->normalize($object->getCommand()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof QueueCommand;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        Assertion::choicesNotEmpty($data, ['class', 'name']);

        Assertion::classExists($data['class']);

        $object = new QueueCommand($this->aggregate->denormalize($data['data'], $data['class']), $data['name']);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === QueueCommand::class;
    }
}
