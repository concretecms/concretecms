<?php

namespace Concrete\Core\Package\Packer;

use InvalidArgumentException;
use SplFileInfo;

class PackerFile
{
    const TYPE_OTHER = 0;
    const TYPE_TRANSLATIONS_POT = 1;
    const TYPE_TRANSLATIONS_PO = 2;
    const TYPE_SVGICON_PACKAGE = 3;
    const TYPE_SVGICON_BLOCKTYPE = 4;
    const TYPE_SVGICON_THEME = 5;

    /**
     * @var string
     */
    private $absolutePath;

    /**
     * @var string
     */
    private $relativePath;

    /**
     * @var bool
     */
    private $isDirectory;

    /**
     * @var bool
     */
    private $isModified;

    /**
     * @var string|null
     */
    private $basename;

    /**
     * @var string|null
     */
    private $extension;

    /**
     * @var int|null
     */
    private $type;

    /**
     * @param string $basePath
     * @param string $absolutePath
     * @param bool $isDirectory
     * @param bool $isModified
     * @param mixed $relativePath
     */
    private function __construct($absolutePath, $relativePath, $isDirectory, $isModified)
    {
        $this->absolutePath = $absolutePath;
        $this->relativePath = $relativePath;
        $this->isDirectory = $isDirectory;
        $this->isModified = $isModified;
    }

    /**
     * @param string $basePath
     * @param \SplFileInfo $fileInfo
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public static function fromSourceFileInfo($basePath, SplFileInfo $fileInfo)
    {
        $absolutePath = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $fileInfo->getPathname()), '/');
        $basePath = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $basePath), '/');
        $basePathLength = strlen($basePath) + 1;
        if (strlen($absolutePath) <= $basePathLength || strpos($absolutePath, $basePath . '/') !== 0) {
            throw new InvalidArgumentException();
        }

        return new static($absolutePath, substr($absolutePath, $basePathLength), $fileInfo->isDir(), false);
    }

    /**
     * @param static $originalFile
     * @param string $actualSourcePath
     *
     * @throws \InvalidArgumentException
     *
     * @return \Concrete\Core\Package\Packer\PackerFile
     */
    public static function newChangedFile(PackerFile $originalFile, $actualSourcePath)
    {
        if ($originalFile->isDirectory) {
            throw new InvalidArgumentException();
        }

        return new static(rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $actualSourcePath), '/'), $originalFile->relativePath, false, true);
    }

    /**
     * @param string $absolutePath
     * @param string $relativePath
     */
    public function newlyCreatedFile($absolutePath, $relativePath)
    {
        return new static(str_replace(DIRECTORY_SEPARATOR, '/', $absolutePath), str_replace(DIRECTORY_SEPARATOR, '/', $relativePath), false, true);
    }

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->absolutePath;
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * @return bool
     */
    public function isDirectory()
    {
        return $this->isDirectory;
    }

    /**
     * @return bool
     */
    public function isModified()
    {
        return $this->isModified;
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        if ($this->basename === null) {
            $this->basename = basename($this->relativePath);
        }

        return $this->basename;
    }

    /**
     * Lower case, wihout dot.
     *
     * @return string
     */
    public function getExtension()
    {
        if ($this->extension === null) {
            $p = strrpos($this->basename, '.');
            $this->extension = $p === false ? '' : strtolower(substr($this->basename, $p + 1));
        }

        return $this->extension;
    }

    /**
     * @return int
     */
    public function getType()
    {
        if ($this->type === null) {
            if (preg_match('/^languages\/messages\.pot$/', $this->relativePath)) {
                $this->type = static::TYPE_TRANSLATIONS_POT;
            } elseif (preg_match('/^languages\/[\w+\-]+\/LC_MESSAGES\/messages\.po$/', $this->relativePath)) {
                $this->type = static::TYPE_TRANSLATIONS_PO;
            } elseif (preg_match('/^icon.svg$/', $this->relativePath)) {
                $this->type = static::TYPE_SVGICON_PACKAGE;
            } elseif (preg_match('/^blocks\/[\w\-]+\/icon\.svg$/', $this->relativePath)) {
                $this->type = static::TYPE_SVGICON_BLOCKTYPE;
            } elseif (preg_match('/^themes\/[\w\-]+\/thumbnail\.svg/', $this->relativePath)) {
                $this->type = static::TYPE_SVGICON_THEME;
            } else {
                $this->type = static::TYPE_OTHER;
            }
        }

        return $this->type;
    }
}
