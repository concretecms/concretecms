<?php

namespace Concrete\Core\Package\Packer\Filter;

use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Package\Packer\PackerFile;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class SvgIconRasterizer implements FilterInterface
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
     * @var string
     */
    protected $coreVersion;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * @param bool $checkEditDate
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Concrete\Core\File\Service\VolatileDirectory $volatileDirectory
     * @param string $coreVersion
     * @param \Illuminate\Filesystem\Filesystem $fs
     * @param OutputInterface $output
     */
    public function __construct($checkEditDate, OutputInterface $output, VolatileDirectory $volatileDirectory, $coreVersion, Filesystem $fs)
    {
        $this->checkEditDate = $checkEditDate;
        $this->output = $output;
        $this->volatileDirectory = $volatileDirectory;
        $this->coreVersion = $coreVersion;
        $this->fs = $fs;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Packer\Filter\FilterInterface::apply()
     */
    public function apply(PackerFile $file)
    {
        if (!$file->isDirectory()) {
            return [$file];
        }
        switch ($file->getType()) {
            case PackerFile::TYPE_SVGICON_BLOCKTYPE:
                $bitmapExtension = 'png';
                $bitmapSize = version_compare($this->coreVersion, '5.7') < 0 ? [16, 16] : [50, 50];
                break;
            case PackerFile::TYPE_SVGICON_PACKAGE:
                $bitmapExtension = 'png';
                $bitmapSize = version_compare($this->coreVersion, '5.7') < 0 ? [97, 97] : [200, 200];
                break;
            case PackerFile::TYPE_SVGICON_THEME:
                $bitmapExtension = 'png';
                $bitmapSize = [120, 90];
                break;
            default:
                return [$file];
        }
        if ($this->checkEditDate) {
            $originalBitmapFile = substr($file->getAbsolutePath(), 0, -strlen($file->getExtension())) . $bitmapExtension;
            if ($this->fs->isFile($originalBitmapFile)) {
                $vectorDate = $this->fs->lastModified($file->getAbsolutePath());
                $bitmapDate = $this->fs->lastModified($originalBitmapFile);
                if (!$vectorDate || !$bitmapDate || $vectorDate < $bitmapDate) {
                    if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
                        $this->output->writeln(t("Skipping generation of bitmap icon for file %s because it's newer than the vector icon", $file->getRelativePath()));
                    }

                    return [$file];
                }
            }
        }
        if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
            $this->output->writeln(t('Generating the bitmap icon from the vector file %s', $file->getRelativePath()));
        }
        $newFileAbs = $this->createBitmap($file, $bitmapExtension, $bitmapSize);
        $newFileRel = substr($file->getRelativePath(), 0, -strlen($file->getExtension())) . $bitmapExtension;

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
    protected function createBitmap(PackerFile $sourceFile, $bitmapExtension, array $bitmapSize)
    {
        $bitmapPath = tempnam($this->volatileDirectory->getPath(), 'ico');
        $cmd = 'inkscape';
        $cmd .= ' --file=' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, $sourceFile->getAbsolutePath()));
        $cmd .= ' --export-' . $bitmapExtension . '=' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, $bitmapPath));
        $cmd .= ' --export-area-page';
        $cmd .= ' --export-width=' . $bitmapSize[0];
        $cmd .= ' --export-height=' . $bitmapSize[0];
        $cmd .= ' 2>&1';
        $output = [];
        $rc = -1;
        @exec($cmd, $output, $rc);
        if ($rc !== 0) {
            throw new RuntimeException(t('Error invoking inkscape to rasterize an image: %s', trim(implode("\n", $output))), $rc);
        }

        return $bitmapPath;
    }
}
