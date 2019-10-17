<?php

namespace Concrete\Core\Package\Packer\Writer;

use Concrete\Core\Package\Packer\PackerFile;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

/**
 * Class that generate a ZIP archive containing the package files.
 */
class Zipper implements WriterInterface
{
    /**
     * The absolute path to the ZIP archive to be created.
     *
     * @var string
     */
    protected $zipFilename;

    /**
     * The root directory inside the ZIP archive (empty string to not create a main directory inside the ZIP archive).
     *
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
     * Initialize the instance.
     *
     * @param string $zipFilename the absolute path to the ZIP archive to be created
     * @param string $rootDirectory the root directory inside the ZIP archive (empty string to not create a main directory inside the ZIP archive)
     * @param \Illuminate\Filesystem\Filesystem $fs
     * @param \Symfony\Component\Console\Output\OutputInterface $output
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
            $this->output->writeln(t('Creating ZIP archive: %s', $this->zipFilename));
        }
        $zipArchive = $this->createZipArchive();
        $success = false;
        try {
            $prefix = $this->addRootDirectory($zipArchive);
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
     * Create the ZIP archive.
     *
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
     * Create the root directory inside the archive (if specified), and returns the prefix for further files/directoryes added to the archive.
     *
     * @param \ZipArchive $zipArchive
     *
     * @throws \RuntimeException
     */
    protected function addRootDirectory(ZipArchive $zipArchive)
    {
        if ($this->rootDirectory === '') {
            return '';
        }
        $path = '';
        foreach (explode('/', $this->rootDirectory) as $chunk) {
            $path = ltrim("{$path}/{$chunk}", '/');
            $this->addDirectory($zipArchive, $path);
        }

        return $this->rootDirectory . '/';
    }

    /**
     * Add a directory to a ZIP archive.
     *
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
        throw new RuntimeException(t('Failed to add a directory to the ZIP archive %1$s: %2$s', $this->zipFilename, $err));
    }

    /**
     * Add a directory to a ZIP archive.
     *
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
        throw new RuntimeException(t('Failed to add the file %1$s to the ZIP archive %2$s: %3$s', $sourceFile, $this->zipFilename, $err));
    }
}
