<?php

namespace Concrete\Core\Command\Process\Logger;

use Concrete\Core\Entity\Command\Process;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FileLogger implements LoggerInterface, NormalizableInterface, DenormalizableInterface
{

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var Process
     */
    protected $process;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * FileLogger constructor.
     * @param string $directory
     * @param Process $process
     */
    public function __construct(string $directory = null, Process $process = null)
    {
        $this->filesystem = new Filesystem();
        $this->directory = $directory;
        $this->process = $process;
    }

    protected function getLogFileName()
    {
        $date = date('Y-m-d-H-i', $this->process->getDateStarted());
        return snake_case($this->process->getName()) . '-' . $date . '-' . $this->process->getID() . '.log';
    }

    protected function getFilePath()
    {
        if (!isset($this->filePath)) {
            $this->filePath = $this->directory . $this->getLogFileName();
        }
        return $this->filePath;
    }

    public function write($message)
    {
        $this->filesystem->append($this->getFilePath(), $message . "\n");
    }

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        return [
            'filePath' => $this->getFilePath(),
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $this->filePath = $data['filePath'];
    }

    public function remove(): void
    {
        if ($this->logExists()) {
            $this->filesystem->delete($this->getFilePath());
        }
    }

    public function readAsArray(): array
    {
        $output = [];
        if ($this->logExists()) {
            $output = explode("\n", trim($this->filesystem->get($this->getFilePath())));
        }
        return $output;
    }

    public function logExists(): bool
    {
        return $this->filesystem->exists($this->getFilePath());
    }

}
