<?php
namespace Concrete\Core\Health\Report\Message;

use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Health\Report\Test\PageTestInterface;
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

    /**
     * @var string|null
     */
    protected $resultId;

    public function __construct(string $resultId = null, TestInterface $test = null)
    {
        $this->resultId = $resultId;
        $this->test = $test;
    }

    /**
     * @return string|null
     */
    public function getResultId(): ?string
    {
        return $this->resultId;
    }

    /**
     * @param string|null $resultId
     */
    public function setResultId(?string $resultId): void
    {
        $this->resultId = $resultId;
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
        $data = [
            'class' => get_class($this->test),
            'test' => $normalizer->normalize($this->test),
            'resultId' => $this->getResultId(),
        ];
        return $data;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $test = app($data['class']);
        if ($test instanceof PageTestInterface) {
            $test->setPageId((int) $data['test']['pageId']);
        }
        $this->resultId = $data['resultId'];
        $this->test = $test;
    }



}