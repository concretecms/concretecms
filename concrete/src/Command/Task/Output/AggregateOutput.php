<?php

namespace Concrete\Core\Command\Task\Output;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AggregateOutput implements OutputInterface, NormalizableInterface, DenormalizableInterface
{

    /**
     * @var OutputInterface[]
     */
    protected $outputs = [];

    /**
     * AggregateOutput constructor.
     * @param OutputInterface[] $outputs
     */
    public function __construct(array $outputs = [])
    {
        $this->outputs = $outputs;
    }

    public function addOutput(OutputInterface $output)
    {
        $this->outputs[] = $output;
    }

    public function write($message)
    {
        foreach($this->outputs as $output) {
            $output->write($message);
        }
    }

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        $outputs = [];
        foreach($this->outputs as $output) {
            $outputs[] = ['type' => get_class($output), 'output' => $normalizer->normalize($output)];
        }
        return ['outputs' => $outputs];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $outputs = [];
        foreach($data['outputs'] as $dataOutput) {
            $output = $denormalizer->denormalize($dataOutput['output'], $dataOutput['type']);
            $this->outputs[] = $output;
        }
    }

}
