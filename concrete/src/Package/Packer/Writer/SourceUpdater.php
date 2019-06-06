<?php

namespace Concrete\Core\Package\Packer\Writer;

use Concrete\Core\Package\Packer\PackerFile;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class that updates the source package directory.
 */
class SourceUpdater implements WriterInterface
{
    /**
     * The path to the package directory (with directory separators normalized to '/', without trailing slashes).
     *
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
     * Initialize the instance.
     *
     * @param string $basePath the path to the package directory (with directory separators normalized to '/', without trailing slashes)
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
     * @see \Concrete\Core\Package\Packer\Writer\WriterInterface::add()
     */
    public function add(PackerFile $file)
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
            $this->saveFile($this->filesToReplace[$relativePath], $relativePath);
            unset($this->filesToReplace[$relativePath]);
        }
    }

    /**
     * Copy the contents of a source file to the package source directory.
     *
     * @param string $sourceAbsolutePath The absolute path to the file to be copied.$this
     * @param string $destinationRelativePath the path to the destination file relative to the package root directory
     */
    protected function saveFile($sourceAbsolutePath, $destinationRelativePath)
    {
        $newContents = $this->fs->get($sourceAbsolutePath);
        if (!is_string($newContents)) {
            throw new RuntimeException(t('Failed to read from file %s', $sourceAbsolutePath));
        }
        $destinationAbsolutePath = $this->basePath . '/' . $destinationRelativePath;
        if ($this->fs->put($destinationAbsolutePath, $newContents) === false) {
            throw new RuntimeException(t('Failed to write to file %s', $destinationRelativePath));
        }
    }
}
