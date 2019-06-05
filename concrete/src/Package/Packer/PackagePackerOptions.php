<?php

namespace Concrete\Core\Package\Packer;

use Concrete\Core\Package\Packer\Filter\FileExcluder;
use InvalidArgumentException;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Options for the PackagePacker class.
 */
class PackagePackerOptions
{
    /**
     * Short tags conversion: convert all short PHP tags to long PHP tags (including short echo tags).
     *
     * @var string
     */
    const CONVERTSHORTTAGS_ALL = 'all';

    /**
     * Short tags conversion: convert short PHP tags to long PHP tags (excluding short echo tags).
     *
     * @var string
     */
    const CONVERTSHORTTAGS_KEEPECHO = 'keep-echo';

    /**
     * Short tags conversion: do not convert any short PHP tags.
     *
     * @var string
     */
    const CONVERTSHORTTAGS_NO = 'no';

    /**
     * Short tags conversion: use CONVERTSHORTTAGS_ALL for packages for concrete5 5.x, CONVERTSHORTTAGS_KEEPECHO for packages for concrete5 8+.
     *
     * @var string
     */
    const CONVERTSHORTTAGS_AUTO = 'auto';

    /**
     * Short tags conversion.
     *
     * @var string
     */
    private $shortTagsConversion = self::CONVERTSHORTTAGS_AUTO;

    /**
     * Flag describing the "extra" files to be included in the archive.
     *
     * @var int
     */
    private $keepFiles = FileExcluder::KEEPFILES_NONE;

    /**
     * Should the source .po translation files be compiled to .mo binary files?
     *
     * @var bool|null
     */
    private $compileTranslations;

    /**
     * Should the source .svg files for icons (package/theme/block type) be compiled to .png image files?
     *
     * @var bool|null
     */
    private $compileIcons;

    /**
     * Should the operation update the source files?
     *
     * @var bool
     */
    private $updateSourceFiles = false;

    /**
     * The path of the ZIP archive to be created.
     *
     * @var string|true
     */
    private $destinationArchivePath = '';

    /**
     * Use the package handle as the root directory inside the archive?
     *
     * @var bool
     */
    private $packageHandleAsArchiveRoot = true;

    /**
     * Where output messages should be sent to.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface|null
     */
    private $output;

    /**
     * Create a new instance.
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Get the short tags conversion.
     *
     * @return string one of the CONVERTSHORTTAGS_... constants
     */
    public function getShortTagsConversion()
    {
        return $this->shortTagsConversion;
    }

    /**
     * Set the short tags conversion.
     *
     * @param string $value one of the CONVERTSHORTTAGS_... constants
     *
     * @throws \InvalidArgumentException if $value is not valid
     *
     * @return $this
     */
    public function setShortTagsConversion($value)
    {
        $value = (string) $value;
        if (!$this->isValueValidForShortTagsConversion($value)) {
            throw new InvalidArgumentException(t('Invalid parameter value (%1$s) for the method %2$s', $value, __METHOD__));
        }
        $this->shortTagsConversion = $value;

        return $this;
    }

    /**
     * Get the flag describing the "extra" files to be included in the archive.
     *
     * @return int flags described by the FileExcluder::KEEPFILES_... constants
     */
    public function getKeepFiles()
    {
        return $this->keepFiles;
    }

    /**
     * Set the flag describing the "extra" files to be included in the archive.
     *
     * @param int $value flags described by the FileExcluder::KEEPFILES_... constants
     *
     * @return $this
     */
    public function setKeepFiles($value)
    {
        $this->keepFiles = (int) $value;

        return $this;
    }

    /**
     * Should the source .po translation files be compiled to .mo binary files?
     *
     * @return bool|null returns TRUE if yes, FALSE if no, NULL to automatically detect if compilation should be performed
     */
    public function isCompileTranslations()
    {
        return $this->compileTranslations;
    }

    /**
     * Should the source .po translation files be compiled to .mo binary files?
     *
     * @param bool|null $value TRUE if yes, FALSE if no, NULL to automatically detect if compilation should be performed
     *
     * @return $this
     */
    public function setCompileTranslations($value)
    {
        $this->compileTranslations = $value === null || $value === '' ? null : (bool) $value;

        return $this;
    }

    /**
     * Should the source .svg files for icons (package/theme/block type) be compiled to .png image files?
     *
     * @return bool|null returns TRUE if yes, FALSE if no, NULL to automatically detect if compilation should be performed
     */
    public function isCompileIcons()
    {
        return $this->compileIcons;
    }

    /**
     * Should the source .svg files for icons (package/theme/block type) be compiled to .png image files?
     *
     * @param bool|null $value TRUE if yes, FALSE if no, NULL to automatically detect if compilation should be performed
     *
     * @return $this
     */
    public function setCompileIcons($value)
    {
        $this->compileIcons = $value === null || $value === '' ? null : (bool) $value;

        return $this;
    }

    /**
     * Should the operation update the source files?
     *
     * @return bool
     */
    public function isUpdateSourceFiles()
    {
        return $this->updateSourceFiles;
    }

    /**
     * Should the operation update the source files?
     *
     * @param bool|null
     * @param mixed $value
     *
     * @return $this
     */
    public function setUpdateSourceFiles($value)
    {
        $this->updateSourceFiles = (bool) $value;

        return $this;
    }

    /**
     * Get the full path to the .zip archive to be created.
     *
     * @return string Return an empty string if no .zip archive should be created or if the destination path should be calculated automatically
     *
     * @see \Concrete\Core\Package\Packer\PackagePackerOptions::isDestinationArchivePathAutomatic()
     */
    public function getDestinationArchivePath()
    {
        return $this->destinationArchivePath === true ? '' : (string) $this->destinationArchivePath;
    }

    /**
     * Get the full path to the .zip archive to be created.
     *
     * @param string $value an empty string if no .zip archive should be created.
     *
     * @return $this
     */
    public function setDestinationArchivePath($value)
    {
        $this->destinationArchivePath = (string) $value;

        return $this;
    }

    /**
     * Should the full path to the .zip archive be determined automatically?
     *
     * @return bool
     *
     * @see \Concrete\Core\Package\Packer\PackagePackerOptions::getDestinationArchivePath()
     */
    public function isDestinationArchivePathAutomatic()
    {
        return $this->destinationArchivePath === true;
    }

    /**
     * Should the full path to the .zip archive be determined automatically?
     *
     * @return $this
     */
    public function setDestinationArchivePathAutomatic()
    {
        $this->destinationArchivePath = true;

        return $this;
    }

    /**
     * Use the package handle as the root directory inside the archive?
     *
     * @return bool
     */
    public function isPackageHandleAsArchiveRoot()
    {
        return $this->packageHandleAsArchiveRoot;
    }

    /**
     * Get where output messages should be sent to.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        if ($this->output === null) {
            $this->output = new NullOutput();
        }

        return $this->output;
    }

    /**
     * Set where output messages should be sent to.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $value
     *
     * @return $this
     */
    public function setOutput(OutputInterface $value)
    {
        $this->output = $value;

        return $this;
    }

    /**
     * Use the package handle as the root directory inside the archive?
     *
     * @param bool $value
     *
     * @return $this;s
     */
    public function setPackageHandleAsArchiveRoot($value)
    {
        $this->packageHandleAsArchiveRoot = (bool) $value;

        return $this;
    }

    /**
     * Check if a value is valid for ShortTagConversion.
     *
     * @param string|mixed $value
     *
     * @return bool
     */
    protected function isValueValidForShortTagsConversion($value)
    {
        return in_array($value, [static::CONVERTSHORTTAGS_ALL, static::CONVERTSHORTTAGS_KEEPECHO, static::CONVERTSHORTTAGS_NO, static::CONVERTSHORTTAGS_AUTO, true]);
    }
}
