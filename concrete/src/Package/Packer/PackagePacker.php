<?php

namespace Concrete\Core\Package\Packer;

use Concrete\Core\Package\Offline\PackageInfo;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use SplFileInfo;

/**
 * Class to create archives from package source files.
 */
class PackagePacker
{
    /**
     * Apply the filters and the writers to the package described by $packageInfo.
     *
     * @param \Concrete\Core\Package\Offline\PackageInfo $packageInfo
     * @param \Concrete\Core\Package\Packer\Filter\FilterInterface[] $filters
     * @param \Concrete\Core\Package\Packer\Writer\WriterInterface[] $writers
     *
     * @throws \RuntimeException in case of processing problems
     */
    public function process(PackageInfo $packageInfo, array $filters, array $writers)
    {
        foreach ($this->generateFileList($packageInfo->getPackageDirectory(), $packageInfo->getPackageDirectory(), $filters) as $packerFile) {
            foreach ($writers as $writer) {
                $writer->add($packerFile);
            }
        }
        foreach ($writers as $writer) {
            $writer->completed();
        }
    }

    /**
     * Generate the list of files/directories applying the filters specified.
     *
     * @param string $packageDirectory the path to the package root directory
     * @param string $directoryToParse the path to be analyzed
     * @param \Concrete\Core\Package\Packer\Filter\FilterInterface[] $filters the filters to be applied
     *
     * @return \Concrete\Core\Package\Packer\PackerFile[]|\Generator
     */
    protected function generateFileList($packageDirectory, $directoryToParse, array $filters)
    {
        $subDirectories = [];
        $iterator = new RecursiveDirectoryIterator($directoryToParse, FilesystemIterator::UNIX_PATHS | FilesystemIterator::SKIP_DOTS);
        foreach ($iterator as $originalFile) {
            if (in_array($originalFile->getFilename(), ['.', '..'], true)) {
                continue;
            }
            foreach ($this->applyFilters($packageDirectory, $originalFile, $filters) as $processedFile) {
                if ($processedFile->isDirectory()) {
                    $subDirectories[] = $processedFile->getAbsolutePath();
                }
                yield $processedFile;
            }
        }
        foreach ($subDirectories as $subDirectory) {
            foreach ($this->generateFileList($packageDirectory, $subDirectory, $filters) as $processedFile) {
                yield $processedFile;
            }
        }
    }

    /**
     * Apply the filters to a file found in the package directory or in one of its sub-directories.
     *
     * @param string $packageDirectory the path to the package root directory
     * @param \SplFileInfo $source the file to be processes
     * @param \Concrete\Core\Package\Packer\Filter\FilterInterface[] $filters the filters to be applied
     *
     * @return \Concrete\Core\Package\Packer\PackerFile[]
     */
    protected function applyFilters($packageDirectory, SplFileInfo $source, array $filters)
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
