<?php
namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Task\Runner\LoggableToFileRunnerInterface;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class RunnerFileLogger implements OutputInterface, NormalizableInterface, DenormalizableInterface
{

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var LoggableToFileRunnerInterface
     */
    protected $runner;

    /**
     * @var string
     */
    protected $directory;

    /**
     * FileOutput constructor.
     * @param string $filePath
     */
    public function __construct(string $directory = null, LoggableToFileRunnerInterface $runner = null)
    {
        $this->filesystem = new Filesystem();
        $this->directory = $directory;
        $this->runner = $runner;
    }

    protected function getFilePath()
    {
        if (!isset($this->filePath)) {
            $this->filePath = $this->directory . $this->runner->getLogFileName();
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


}
