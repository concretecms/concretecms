<?php
namespace Concrete\Core\Foundation\Queue\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Normalt\Normalizer\AggregateNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SerializerManager
{
    protected $normalizers = array();

    public function addNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizers[] = $normalizer;
    }

    public function prependNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizers = array_prepend($this->normalizers, $normalizer);
    }

    /**
     * @return array
     */
    public function getNormalizers()
    {
        return $this->normalizers;
    }

    public function getAggregateNormalizer()
    {
        $normalizer = new AggregateNormalizer($this->normalizers);
        return $normalizer;
    }

    public function getSerializer()
    {
        // Important note: we used to use the Bernard serializer here
        // but I got so 1@#$! tired of "serializer not a normalizer" errors
        // that I wanted to kill myself.
        $serializer = new SymfonySerializer([$this->getAggregateNormalizer()], [new JsonEncoder()]);
        $serializer = new Serializer($serializer);
        return $serializer;
    }


}
