<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Foundation\Command\Command;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class HandleProcessMessageCommand extends Command implements NormalizableInterface, DenormalizableInterface, ProcessMessageInterface
{

    /**
     * @var string
     */
    protected $processId;

    /**
     * @var object
     */
    protected $message;

    public function __construct(string $processId = null, $message = null)
    {
        $this->processId = $processId;
        $this->message = $message;
    }

    /**
     * @return object
     */
    public function getMessage(): object
    {
        return $this->message;
    }

    /**
     * @param object $message
     */
    public function setMessage(object $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getProcess(): string
    {
        return $this->processId;
    }

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        return [
            'type' => get_class($this->message),
            'message' => $normalizer->normalize($this->message),
            'process' => $this->processId,
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $message = $denormalizer->denormalize($data['message'], $data['type']);
        $this->processId = $data['process'];
        $this->message = $message;
    }

}
