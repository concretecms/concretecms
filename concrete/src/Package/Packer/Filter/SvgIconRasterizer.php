<?php

namespace Concrete\Core\Package\Packer\Filter;

use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Package\Packer\PackerFile;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate bitmap icons starting from source SVG icons.
 */
class SvgIconRasterizer implements FilterInterface
{
    /**
     * The concrete5 version for which the bitmap icons should be created for.
     *
     * @var string
     */
    protected $coreVersion;

    /**
     * Should the bitmap icons be created only if they don't exist, or if they are older than the source SVG files?
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
     * @param string $coreVersion the concrete5 version for which the bitmap icons should be created for
     * @param bool $checkEditDate set to true if the bitmap icons should be created only if they don't exist, or if they are older than the source SVG files
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Concrete\Core\File\Service\VolatileDirectory $volatileDirectory
     * @param \Illuminate\Filesystem\Filesystem $fs
     */
    public function __construct($coreVersion, $checkEditDate, OutputInterface $output, VolatileDirectory $volatileDirectory, Filesystem $fs)
    {
        $this->coreVersion = $coreVersion;
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
        if (!$file->isDirectory()) {
            return [$file];
        }
        $bitmapData = $this->getBitmapData($file);
        if ($bitmapData === null) {
            return [$file];
        }
        if ($this->checkEditDate) {
            if ($this->isBitmapNewerThanSVG($file, $bitmapData)) {
                if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
                    $this->output->writeln(t("Skipping generation of bitmap icon for file %s because it's newer than the vector icon", $file->getRelativePath()));
                }

                return [$file];
            }
        }
        if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
            $this->output->writeln(t('Generating the bitmap icon from the vector file %s', $file->getRelativePath()));
        }
        $newFileAbs = $this->createBitmap($file, $bitmapData);
        $bitmapRelativePath = $file->getRelativePathWithExtension($bitmapData['extension']);

        return [$file, PackerFile::newlyCreatedFile($newFileAbs, $bitmapRelativePath)];
    }

    /**
     * Get the details of the bitmap data to be generated for a file.
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $file
     *
     * @return array|null return an array with keys ['extension', 'width', 'height'] if there's a bitmap file associated to the file, NULL otherwise
     */
    protected function getBitmapData(PackerFile $file)
    {
        switch ($file->getType()) {
            case PackerFile::TYPE_SVGICON_BLOCKTYPE:
                return version_compare($this->coreVersion, '5.7') < 0 ? ['extension' => 'png', 'width' => 16, 'height' => 16] : ['extension' => 'png', 'width' => 50, 'height' => 50];
            case PackerFile::TYPE_SVGICON_PACKAGE:
                return version_compare($this->coreVersion, '5.7') < 0 ? ['extension' => 'png', 'width' => 97, 'height' => 97] : ['extension' => 'png', 'width' => 200, 'height' => 200];
            case PackerFile::TYPE_SVGICON_THEME:
                return ['extension' => 'png', 'width' => 120, 'height' => 90];
            default:
                return null;
        }
    }

    /**
     * Check if there's already a bitmap file, and that it's not older than the SVG file.
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $file the SVG file
     * @param array $bitmapData the result of the getBitmapData() method
     *
     * @return bool return true if the bitmap file already exists and it's newer than the SVG file
     */
    protected function isBitmapNewerThanSVG(PackerFile $file, array $bitmapData)
    {
        $bitmapFile = $file->getAbsolutePathWithExtension($bitmapData['extension']);
        if (!$this->fs->isFile($bitmapFile)) {
            return false;
        }
        $svgTimestamp = $this->fs->lastModified($file->getAbsolutePath());
        $bitmapTimestamp = $this->fs->lastModified($bitmapFile);

        return $svgTimestamp && $bitmapTimestamp && $svgTimestamp < $bitmapTimestamp;
    }

    /**
     * Create the bitmap associated to an SVG file.
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $sourceFile the SVG file
     * @param array $bitmapData the result of the getBitmapData() method
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function createBitmap(PackerFile $sourceFile, array $bitmapData)
    {
        $bitmapPath = tempnam($this->volatileDirectory->getPath(), 'ico');
        $cmd = 'inkscape';
        $cmd .= ' --file=' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, $sourceFile->getAbsolutePath()));
        $cmd .= ' --export-' . $bitmapData['extension'] . '=' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, $bitmapPath));
        $cmd .= ' --export-area-page';
        $cmd .= ' --export-width=' . $bitmapData['width'];
        $cmd .= ' --export-height=' . $bitmapData['height'];
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
