<?php

namespace Concrete\Core\Package\Packer\Filter;

use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Package\Packer\PackerFile;
use Gettext\Translations;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class TranslationCompiler implements FilterInterface
{
    /**
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
     * @param bool $checkEditDate
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Concrete\Core\File\Service\VolatileDirectory $volatileDirectory
     * @param \Illuminate\Filesystem\Filesystem $fs
     */
    public function __construct($checkEditDate, OutputInterface $output, VolatileDirectory $volatileDirectory, Filesystem $fs)
    {
        $this->checkEditDate = $checkEditDate;
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
        if ($file->isDirectory()) {
            return [$file];
        }
        if ($file->getType() !== PackerFile::TYPE_TRANSLATIONS_PO) {
            return [$file];
        }

        if ($this->checkEditDate) {
            $originalMOFile = substr($file->getAbsolutePath(), 0, -strlen($file->getExtension())) . 'mo';
            if ($this->fs->isFile($originalMOFile)) {
                $poDate = $this->fs->lastModified($file->getAbsolutePath());
                $moDate = $this->fs->lastModified($originalMOFile);
                if (!$poDate || !$moDate || $poDate < $moDate) {
                    if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
                        $this->output->writeln(t("Skipping generation of compiled language file %s because it's newer than the source language file", $file->getRelativePath()));
                    }

                    return [$file];
                }
            }
        }
        if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
            $this->output->writeln(t('Compiling the source language file %s', $file->getRelativePath()));
        }
        $newFileAbs = $this->createMO($file);
        $newFileRel = substr($file->getRelativePath(), 0, -strlen($file->getExtension())) . 'mo';

        return [$file, PackerFile::newlyCreatedFile($newFileAbs, $newFileRel)];
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
