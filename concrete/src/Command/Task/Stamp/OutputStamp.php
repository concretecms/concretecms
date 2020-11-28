<?php

namespace Concrete\Core\Command\Task\Stamp;

use Concrete\Core\Command\Task\Output\OutputInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OutputStamp implements StampInterface, NormalizableInterface, DenormalizableInterface
{

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * OutputStamp constructor.
     * @param $output
     */
    public function __construct(OutputInterface $output = null)
    {
        $this->output = $output;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        return [
            'type' => get_class($this->output),
            'output' => $normalizer->normalize($this->output),
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $output = $denormalizer->denormalize($data['output'], $data['type']);
        $this->output = $output;
    }

}
