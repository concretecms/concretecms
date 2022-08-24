<?php
namespace Concrete\Core\Health\Report\Message;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Health\Report\Test\TestInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RunReportTestMessage extends Command implements NormalizableInterface, DenormalizableInterface
{

    /**
     * @var TestInterface
     */
    protected $test;

    public function __construct(TestInterface $test = null)
    {
        $this->test = $test;
    }

    /**
     * @return TestInterface
     */
    public function getTest(): TestInterface
    {
        return $this->test;
    }

    /**
     * @param TestInterface $test
     */
    public function setTest(TestInterface $test): void
    {
        $this->test = $test;
    }

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        return [
            'type' => get_class($this->test),
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $test = app($data['type']);
        $this->test = $test;
    }



}