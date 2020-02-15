<?php

namespace Concrete\Core\Package\Packer\Writer;

use Concrete\Core\Package\Packer\PackerFile;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class that save the package files to a new directory.
 */
class Cloner implements WriterInterface
{
    /**
     * The absolute path to the destination directory.
     *
     * @var string
     */
    protected $destinationDirectory;

    /**
     * Overwrite files if they already exist in the destination directory?
     *
     * @var bool
     */
    protected $overwrite;

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
    protected $directoriesToAdd = [];

    /**
     * @var array
     */
    protected $filesToAdd = [];

    /**
     * Initialize the instance.
     *
     * @param string $destinationDirectory the absolute path to the destination directory
     * @param bool $overwrite overwrite files if they already exist in the destination directory?
     * @param \Illuminate\Filesystem\Filesystem $fs
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct($destinationDirectory, $overwrite, OutputInterface $output, Filesystem $fs)
    {
        $this->destinationDirectory = $destinationDirectory;
        $this->overwrite = (bool) $overwrite;
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
        $relativePath = $file->getRelativePath();
        if ($file->isDirectory()) {
            if (!in_array($relativePath, $this->directoriesToAdd, true)) {
                $this->directoriesToAdd[] = $relativePath;
            }
        } else {
            if (!isset($this->filesToAdd[$relativePath]) || $file->isModified()) {
                $this->filesToAdd[$relativePath] = $file->getAbsolutePath();
            }
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
            $this->output->writeln(t('Copying package files to %s', $this->destinationDirectory));
        }
        $this->createDestinationDirectory();
        $success = false;
        try {
            foreach ($this->directoriesToAdd as $directoryToAdd) {
                $this->createDirectory($directoryToAdd);
            }
            foreach ($this->filesToAdd as $relativePath => $sourceFile) {
                $this->copyFile($sourceFile, $relativePath);
            }
            $success = true;
        } finally {
            if ($success !== true && !$this->overwrite) {
                $this->fs->deleteDirectory($this->destinationDirectory);
            }
        }
    }

    /**
     * Create the destination directory.
     *
     * @throws \RuntimeException
     */
    protected function createDestinationDirectory()
    {
        if ($this->fs->exists($this->destinationDirectory)) {
            if (!$this->overwrite) {
                throw new RuntimeException(t('The directory %s already exists.', $this->destinationDirectory));
            }
            if ($this->fs->isFile($this->destinationDirectory)) {
                throw new RuntimeException(t('The destination %s is a file, not a directory.', $this->destinationDirectory));
            }

            return;
        }
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln(t('Creating directory %s', $this->destinationDirectory));
        }
        if (!$this->fs->makeDirectory($this->destinationDirectory)) {
            throw new RuntimeException(t('Failed to create the directory %s.', $this->destinationDirectory));
        }
    }

    /**
     * Create a directory in the destination directory.
     *
     * @param string $relativePath
     *
     * @throws \RuntimeException
     */
    protected function createDirectory($relativePath)
    {
        $absoluteDestinationPath = $this->destinationDirectory . '/' . $relativePath;
        if ($this->fs->exists($absoluteDestinationPath)) {
            return;
        }
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln(t('Creating directory %s', $relativePath));
        }
        if (!$this->fs->makeDirectory($absoluteDestinationPath)) {
            throw new RuntimeException(t('Failed to create the directory %s.', $absoluteDestinationPath));
        }
    }

    /**
     * Copy a file.
     *
     * @param string $sourceFile
     * @param string $relativePath
     *
     * @throws \RuntimeException
     */
    protected function copyFile($sourceFile, $relativePath)
    {
        $absolutePath = $this->destinationDirectory . '/' . $relativePath;
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln(t('Copying file %s', $relativePath));
        }
        if (!$this->fs->copy($sourceFile, $absolutePath)) {
            throw new RuntimeException(t('Failed to copy the file %1$s to %2$s', $sourceFile, $relativePath));
        }
    }
}
