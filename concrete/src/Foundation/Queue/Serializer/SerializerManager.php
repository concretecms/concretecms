<?php
namespace Concrete\Core\Foundation\Queue\Serializer;

use Bernard\Serializer;
use Normalt\Normalizer\AggregateNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SerializerManager
{
    protected $normalizers = array();

    public function addNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizers[] = $normalizer;
    }

    /**
     * @return array
     */
    public function getNormalizers()
    {
        return $this->normalizers;
    }

    public function getSerializer()
    {
        $normalizer = new AggregateNormalizer($this->normalizers);
        $serializer = new Serializer($normalizer);
        return $serializer;
    }


}
