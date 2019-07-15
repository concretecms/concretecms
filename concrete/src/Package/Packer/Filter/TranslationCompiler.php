<?php

namespace Concrete\Core\Package\Packer\Filter;

use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Package\Packer\PackerFile;
use Gettext\Translations;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate compiled .mo translation files from source .po translation files.
 */
class TranslationCompiler implements FilterInterface
{
    /**
     * Should the compiled .mo translation files be created only if they don't exist, or if they are older than the source .po translation files?
     *
     * @var bool
     */
    protected $checkEditDate;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \Concrete\Core\File\Service\VolatileDirectory
     */
    protected $volatileDirectory;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Initialize the instance.
     *
     * @param bool $checkEditDate should the compiled .mo translation files be created only if they don't exist, or if they are older than the source .po translation files?
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Concrete\Core\File\Service\VolatileDirectory $volatileDirectory
     * @param \Illuminate\Filesystem\Filesystem $fs
     */
    public function __construct($checkEditDate, OutputInterface $output, VolatileDirectory $volatileDirectory, Filesystem $fs)
    {
        $this->checkEditDate = (bool) $checkEditDate;
        $this->output = $output;
        $this->volatileDirectory = $volatileDirectory;
        $this->fs = $fs;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Packer\Filter\FilterInterface::apply()
     */
    public function apply(PackerFile $file)
    {
        if ($file->isDirectory() || $file->getType() !== PackerFile::TYPE_TRANSLATIONS_PO) {
            return [$file];
        }
        if ($this->checkEditDate) {
            if ($this->isMONewerThanPO($file)) {
                if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
                    $this->output->writeln(t("Skipping compilation of language file %s because it's older than the compiled language file", $file->getRelativePath()));
                }

                return [$file];
            }
        }
        if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
            $this->output->writeln(t('Compiling the source language file %s', $file->getRelativePath()));
        }
        $newFileAbs = $this->createMO($file);
        $moRelativePath = $file->getRelativePathWithExtension('mo');

        return [$file, PackerFile::newlyCreatedFile($newFileAbs, $moRelativePath)];
    }

    /**
     * Check if there's already a .mo file, and that it's not older than the .po file.
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $file the .po file
     *
     * @return bool return true if the .mo file already exists and it's newer than the .po file.
     */
    protected function isMONewerThanPO(PackerFile $file)
    {
        $moFile = $file->getAbsolutePathWithExtension('mo');
        if (!$this->fs->isFile($moFile)) {
            return false;
        }
        $poTimestamp = $this->fs->lastModified($file->getAbsolutePath());
        $moTimestamp = $this->fs->lastModified($moFile);

        return $poTimestamp && $moTimestamp && $poTimestamp < $moTimestamp;
    }

    /**
     * @param \Concrete\Core\Package\Packer\PackerFile $sourceFile
     * @param string $bitmapExtension
     * @param int[] $bitmapSize
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function createMO(PackerFile $sourceFile)
    {
        $moPath = tempnam($this->volatileDirectory->getPath(), 'mo');
        $translations = Translations::fromPoFile($sourceFile->getAbsolutePath());
        if ($translations->toMoFile($moPath) === false) {
            throw new RuntimeException(t('Failed to write compiled translations file to %s', $moPath));
        }

        return $moPath;
    }
}
