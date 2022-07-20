<?php
namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Notification\Events\MercureService;
use Concrete\Core\Notification\Events\ServerEvent\ProcessOutputEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class PushOutput implements OutputInterface, NormalizableInterface, DenormalizableInterface
{

    /**
     * @var MercureService
     */
    protected $service;

    /**
     * @var string
     */
    protected $processId;

    public function __construct(MercureService $service = null, string $processId = null)
    {
        $this->service = $service;
        $this->processId = $processId;
    }

    public function write($message)
    {
        $this->service->getHub()->publish((new ProcessOutputEvent($this->processId, $message))->getUpdate());
    }

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        return [
            'processId' => $this->processId,
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $this->service = app(MercureService::class);
        $this->processId = $data['processId'];
    }

}
