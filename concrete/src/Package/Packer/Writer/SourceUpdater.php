<?php

namespace Concrete\Core\Package\Packer\Writer;

use Concrete\Core\Package\Packer\PackerFile;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class SourceUpdater implements WriterInterface
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * @var array
     */
    protected $filesToReplace = [];

    /**
     * @param string $basePath
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Illuminate\Filesystem\Filesystem $fs
     */
    public function __construct($basePath, OutputInterface $output, Filesystem $fs)
    {
        $this->basePath = $basePath;
        $this->output = $output;
        $this->fs = $fs;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Packer\Writer\WriterInterface::processFile()
     */
    public function processFile(PackerFile $file)
    {
        if (!$file->isDirectory() && $file->isModified()) {
            $this->filesToReplace[$file->getRelativePath()] = $file->getAbsolutePath();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Packer\Writer\WriterInterface::completed()
     */
    public function completed()
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln(t('Updating source directory: %s', $this->basePath));
        }
        foreach (array_keys($this->filesToReplace) as $relativePath) {
            if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                $this->output->writeln(t('Updating file %s', $relativePath));
            }
            $absolutePathOfNewContents = $this->filesToReplace[$relativePath];
            $newContents = $this->fs->get($absolutePathOfNewContents);
            if (!is_string($newContents)) {
                throw new RuntimeException(t('Failed to read from file %s', $absolutePathOfNewContents));
            }
            $absolutePathOfDestinationFile = $this->basePath . '/' . $relativePath;
            if ($this->fs->put($absolutePathOfDestinationFile, $newContents) === false) {
                throw new RuntimeException(t('Failed to write to file %s', $absolutePathOfDestinationFile));
            }
            unset($this->filesToReplace[$relativePath]);
        }
    }
}
