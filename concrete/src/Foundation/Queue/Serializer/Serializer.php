<?php

namespace Concrete\Core\Foundation\Queue\Serializer;

use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * A wrapper for Bernard's serializer.
 * Class Serializer
 * @package Concrete\Core\Foundation\Queue
 */
class Serializer
{

    /**
     * @var SymfonySerializer
     */
    protected $serializer;

    public function __construct(SymfonySerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serialize($data)
    {
        return $this->serializer->serialize($data, 'json');
    }

    public function unserialize($contents)
    {
        $data = json_decode($contents, true);
        return $this->denormalize($data);
    }

    public function normalize($data)
    {
        return $this->serializer->normalize($data);
    }

    public function denormalize($data)
    {
        return $this->serializer->denormalize($data, 'Bernard\Envelope');
    }

}