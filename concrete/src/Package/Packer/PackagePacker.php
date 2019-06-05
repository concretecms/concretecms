<?php

namespace Concrete\Core\Package\Packer;

use Concrete\Core\Application\Application;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Package\Offline\Inspector;
use Concrete\Core\Package\Offline\PackageInfo;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class to create archives from package source files.
 */
class PackagePacker
{
    /**
     * @var \Concrete\Core\Package\Offline\Inspector
     */
    protected $packageInspector;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Initialize the class.
     *
     * @param \Concrete\Core\Package\Offline\Inspector $packageInspector
     * @param \Concrete\Core\Application\Application $app
     */
    public function __construct(Inspector $packageInspector, Application $app)
    {
        $this->packageInspector = $packageInspector;
        $this->app = $app;
    }

    /**
     * @param string $packageDirectory
     * @param \Concrete\Core\Package\Packer\PackagePackerOptions $options
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems analyzing the package
     * @throws \RuntimeException in case of processing problems
     */
    public function process($packageDirectory, PackagePackerOptions $options)
    {
        if ($options->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $options->getOutput()->writeln(t('Checking source package directory'));
        }
        $packageDirectory = $this->checkPackageDirectory($packageDirectory);
        if ($options->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $options->getOutput()->writeln(t('Retrieving package details'));
        }
        $packageInfo = $this->getPackageInfo($packageDirectory);
        $volatileDirectory = $this->app->make(VolatileDirectory::class);
        try {
            if ($options->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $options->getOutput()->writeln(t('Creating filters'));
            }
            $filters = $this->createFilters($volatileDirectory, $options, $packageInfo);
            if ($options->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $options->getOutput()->writeln(t('Creating writers'));
            }
            $writers = $this->createWriters($packageDirectory, $options, $packageInfo);
            if ($options->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $options->getOutput()->writeln(t('Generating file list'));
            }
            foreach ($this->generateFileList($packageDirectory, $packageDirectory, $filters, $options->getOutput()) as $packerFile) {
                foreach ($writers as $writer) {
                    $writer->processFile($packerFile);
                }
            }
            foreach ($writers as $writer) {
                $writer->completed();
            }
        } finally {
            unset($filters);
            unset($writers);
            unset($volatileDirectory);
        }
    }

    /**
     * @param string|mixed $packageDirectory
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function checkPackageDirectory($packageDirectory)
    {
        $packageDirectory = (string) $packageDirectory;
        $result = $packageDirectory !== '' && is_dir($packageDirectory) ? @realpath($packageDirectory) : false;
        $result = is_string($result) ? rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $result), '/') : '';
        if ($result === '') {
            throw new RuntimeException(t('Unable to find the directory %s', $packageDirectory));
        }
        if (!is_file($result . '/' . FILENAME_PACKAGE_CONTROLLER)) {
            throw new RuntimeException(t('The directory %1$s does not contain the file %2$s', $result, FILENAME_PACKAGE_CONTROLLER));
        }

        return $result;
    }

    /**
     * Extract the package details from the packer options.
     *
     * @param string $packageDirectory
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems analyzing the package
     *
     * @return \Concrete\Core\Package\Offline\PackageInfo
     */
    protected function getPackageInfo($packageDirectory)
    {
        return $this->packageInspector->inspectControllerFile($packageDirectory . '/' . FILENAME_PACKAGE_CONTROLLER);
    }

    /**
     * Get the final option controlling the short tag expansion.
     *
     * @param \Concrete\Core\Package\Packer\PackagePackerOptions $options
     * @param \Concrete\Core\Package\Offline\PackageInfo $packageInfo
     *
     * @return string one of the CONVERTSHORTTAGS_... constants
     */
    protected function finalizeTagConversion(PackagePackerOptions $options, PackageInfo $packageInfo)
    {
        $result = $options->getShortTagsConversion();
        if ($result === $options::CONVERTSHORTTAGS_AUTO) {
            $result = version_compare($packageInfo->getMayorMinimumCoreVersion(), '8') < 0 ? $options::CONVERTSHORTTAGS_ALL : $options::CONVERTSHORTTAGS_KEEPECHO;
        }

        return $result;
    }

    /**
     * Get the final option controlling the creation of the .zip archive.
     *
     * @param string $packageDirectory
     * @param \Concrete\Core\Package\Packer\PackagePackerOptions $options
     * @param \Concrete\Core\Package\Offline\PackageInfo $packageInfo
     *
     * @return string empty string if the .zip archive shouldn't be created, its full path otherwise
     */
    protected function finalizeZipFileName($packageDirectory, PackagePackerOptions $options, PackageInfo $packageInfo)
    {
        if (!$options->isDestinationArchivePathAutomatic()) {
            return $options->getDestinationArchivePath();
        }

        return dirname($packageDirectory) . '/' . $packageInfo->getHandle() . '-' . $packageInfo->getVersion() . '.zip';
    }

    /**
     * @param \Concrete\Core\File\Service\VolatileDirectory $volatileDirectory
     * @param \Concrete\Core\Package\Packer\PackagePackerOptions $options
     * @param \Concrete\Core\Package\Offline\PackageInfo $packageInfo
     *
     * @return \Concrete\Core\Package\Packer\Filter\FilterInterface[]
     */
    protected function createFilters(VolatileDirectory $volatileDirectory, PackagePackerOptions $options, PackageInfo $packageInfo)
    {
        $commonOptions = [
            'output' => $options->getOutput(),
            'volatileDirectory' => $volatileDirectory,
        ];
        $result = [];
        $tagConversion = $this->finalizeTagConversion($options, $packageInfo);
        if ($tagConversion !== $options::CONVERTSHORTTAGS_NO) {
            $result[] = $this->app->make(Filter\ShortTagExpander::class, $commonOptions + ['expandEcho' => $tagConversion === $options::CONVERTSHORTTAGS_ALL]);
        }
        if ($options->isCompileIcons() !== false) {
            $result[] = $this->app->make(Filter\SvgIconRasterizer::class, $commonOptions + ['checkEditDate' => $options->isCompileIcons() === null, 'coreVersion' => $packageInfo->getMayorMinimumCoreVersion()]);
        }
        if ($options->isCompileTranslations() !== false) {
            $result[] = $this->app->make(Filter\TranslationCompiler::class, $commonOptions + ['checkEditDate' => $options->isCompileTranslations() === null]);
        }
        $result[] = $this->app->make(Filter\FileExcluder::class, $commonOptions + ['keepFiles' => $options->getKeepFiles()]);

        return $result;
    }

    /**
     * @param string $packageDirectory
     * @param \Concrete\Core\Package\Packer\PackagePackerOptions $options
     * @param \Concrete\Core\Package\Offline\PackageInfo $packageInfo
     *
     * @return \Concrete\Core\Package\Packer\Writer\WriterInterface[]
     */
    protected function createWriters($packageDirectory, PackagePackerOptions $options, PackageInfo $packageInfo)
    {
        $commonOptions = [
            'output' => $options->getOutput(),
        ];
        $result = [];
        if ($options->isUpdateSourceFiles()) {
            $result[] = $this->app->make(Writer\SourceUpdater::class, $commonOptions + ['basePath' => $packageDirectory]);
        }
        $zipFilename = $this->finalizeZipFileName($packageDirectory, $options, $packageInfo);
        if ($zipFilename !== '') {
            $result[] = $this->app->make(Writer\Zipper::class, $commonOptions + [
                'zipFilename' => $zipFilename,
                'rootDirectory' => $options->isPackageHandleAsArchiveRoot() ? $packageInfo->getHandle() : '',
            ]);
        }

        return $result;
    }

    /**
     * @param string $packageDirectory
     * @param string $directoryToParse
     * @param \Concrete\Core\Package\Packer\Filter\FilterInterface[] $filters
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Concrete\Core\Package\Packer\PackerFile|\Generator
     */
    protected function generateFileList($packageDirectory, $directoryToParse, array $filters, OutputInterface $output)
    {
        $prefixLength = strlen($packageDirectory) + 1;
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(t('Processing directory %s', '/' . substr($directoryToParse, $prefixLength)));
        }
        $subDirectories = [];
        $iterator = new RecursiveDirectoryIterator($directoryToParse, FilesystemIterator::UNIX_PATHS | FilesystemIterator::SKIP_DOTS);
        foreach ($iterator as $originalFile) {
            if (in_array($originalFile->getFilename(), ['.', '..'], true)) {
                continue;
            }
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln(t('Processing file %s', '/' . substr($originalFile->getPathname(), $prefixLength)));
            }
            foreach ($this->applyFilters($packageDirectory, $filters, $originalFile, $output) as $processedFile) {
                if ($processedFile->isDirectory()) {
                    $subDirectories[] = $processedFile->getAbsolutePath();
                }
                yield $processedFile;
            }
        }
        foreach ($subDirectories as $subDirectory) {
            foreach ($this->generateFileList($packageDirectory, $subDirectory, $filters, $output) as $processedFile) {
                yield $processedFile;
            }
        }
    }

    /**
     * @param string $packageDirectory
     * @param \Concrete\Core\Package\Packer\Filter\FilterInterface[] $filters
     * @param \SplFileInfo $source
     *
     * @return \Concrete\Core\Package\Packer\PackerFile[]
     */
    protected function applyFilters($packageDirectory, array $filters, SplFileInfo $source)
    {
        $files = [PackerFile::fromSourceFileInfo($packageDirectory, $source)];
        foreach ($filters as $filter) {
            $result = [];
            foreach ($files as $file) {
                foreach ($filter->apply($file) as $filtered) {
                    $result[] = $filtered;
                }
            }
            $files = $result;
        }

        return $files;
    }
}
