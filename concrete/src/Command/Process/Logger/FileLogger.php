<?php

namespace Concrete\Core\Command\Process\Logger;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Command\Process;
use Illuminate\Filesystem\Filesystem;

class FileLogger implements LoggerInterface
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
    public function __construct(string $directory, Process $process)
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
}
