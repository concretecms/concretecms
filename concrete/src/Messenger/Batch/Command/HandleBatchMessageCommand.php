<?php

namespace Concrete\Core\Messenger\Batch\Command;

use Concrete\Core\Foundation\Command\Command;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class HandleBatchMessageCommand extends Command implements NormalizableInterface, DenormalizableInterface, BatchProcessMessageInterface
{

    /**
     * @var object
     */
    protected $message;

    /**
     * @var string
     */
    protected $batchProcess;

    public function __construct(string $batchProcess = null, $message = null)
    {
        $this->batchProcess = $batchProcess;
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
    public function getBatchProcess(): string
    {
        return $this->batchProcess;
    }

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        return [
            'type' => get_class($this->message),
            'message' => $normalizer->normalize($this->message),
            'batchProcess' => $this->batchProcess,
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $message = $denormalizer->denormalize($data['message'], $data['type']);
        $this->batchProcess = $data['batchProcess'];
        $this->message = $message;
    }

}
