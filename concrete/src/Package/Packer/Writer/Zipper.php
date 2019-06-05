<?php

namespace Concrete\Core\Package\Packer\Writer;

use Concrete\Core\Package\Packer\PackerFile;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class Zipper implements WriterInterface
{
    /**
     * @var string
     */
    protected $zipFilename;

    /**
     * @var string
     */
    protected $rootDirectory;

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
     * @param string $zipFilename
     * @param string $rootDirectory
     * @param Filesystem $fs
     * @param OutputInterface $output
     */
    public function __construct($zipFilename, $rootDirectory, OutputInterface $output, Filesystem $fs)
    {
        $this->zipFilename = $zipFilename;
        $this->rootDirectory = trim(str_replace(DIRECTORY_SEPARATOR, '/', $rootDirectory), '/');
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
        if ($file->isDirectory()) {
            if (!in_array($file->getRelativePath(), $this->directoriesToAdd, true)) {
                $this->directoriesToAdd[] = $file->getRelativePath();
            }
        } else {
            $this->filesToAdd[$file->getRelativePath()] = $file->getAbsolutePath();
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
            $this->output->writeln(t('Creating ZIP archive: %s', $this->zipFilename));
        }
        $zipArchive = $this->createZipArchive();
        $success = false;
        try {
            if ($this->rootDirectory === '') {
                $prefix = '';
            } else {
                $this->addRootDirectories($zipArchive);
                $prefix = $this->rootDirectory . '/';
            }
            foreach ($this->directoriesToAdd as $directoryToAdd) {
                $this->addDirectory($zipArchive, $prefix . $directoryToAdd);
            }
            foreach ($this->filesToAdd as $relativePath => $sourceFile) {
                $this->addFile($zipArchive, $sourceFile, $prefix . $relativePath);
            }
            if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->output->writeln(t('Closing ZIP archive'));
            }
            $zipArchive->close();
            $success = true;
        } finally {
            if ($success !== true) {
                $zipArchive->close();
                $this->fs->delete($this->zipFilename);
            }
        }
    }

    /**
     * @throws \RuntimeException
     *
     * @return \ZipArchive
     */
    protected function createZipArchive()
    {
        if ($this->fs->exists($this->zipFilename)) {
            $this->fs->delete($this->zipFilename);
            if ($this->fs->exists($this->zipFilename)) {
                throw new RuntimeException(t('Failed to delete the file %s', $this->zipFilename));
            }
        }
        $zipArchive = new ZipArchive();
        if ($zipArchive->open($this->zipFilename, ZipArchive::CREATE | ZipArchive::CHECKCONS) !== true) {
            $err = (string) $zipArchive->getStatusString();
            if ($err === '') {
                $err = t('Unknown error');
            }
            throw new RuntimeException(t('Failed to create the file %1$s: %2$s', $this->zipFilename, $err));
        }

        return $zipArchive;
    }

    /**
     * @param \ZipArchive $zipArchive
     *
     * @throws \RuntimeException
     */
    protected function addRootDirectories(ZipArchive $zipArchive)
    {
        $previous = '';
        foreach (explode('/', $this->rootDirectory) as $chunk) {
            if ($previous === '') {
                $current = $chunk;
            } else {
                $current .= '/' . $chunk;
            }
            $this->addDirectory($zipArchive, $current);
            $previous = $current;
        }
    }

    /**
     * @param \ZipArchive $zipArchive
     * @param string $relativePath
     *
     * @throws \RuntimeException
     */
    protected function addDirectory(ZipArchive $zipArchive, $relativePath)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln(t('Adding directory to ZIP archive: %s', $relativePath));
        }
        if ($zipArchive->addEmptyDir($relativePath)) {
            return;
        }
        $err = (string) $zipArchive->getStatusString();
        if ($err === '') {
            $err = t('Unknown error');
        }
        throw new RuntimeException(t('Failed to add a directory to the zip archive %1$s: %2$s', $this->zipFilename, $err));
    }

    /**
     * @param \ZipArchive $zipArchive
     * @param string $sourceFile
     * @param string $relativePath
     *
     * @throws \RuntimeException
     */
    protected function addFile(ZipArchive $zipArchive, $sourceFile, $relativePath)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln(t('Adding file to ZIP archive: %s', $relativePath));
        }
        if ($zipArchive->addFile($sourceFile, $relativePath)) {
            return;
        }
        $err = (string) $zipArchive->getStatusString();
        if ($err === '') {
            $err = t('Unknown error');
        }
        throw new RuntimeException(t('Failed to add the file %1$s to the zip archive %2$s: %3$s', $sourceFile, $this->zipFilename, $err));
    }
}
