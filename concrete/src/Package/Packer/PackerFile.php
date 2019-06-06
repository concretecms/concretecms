<?php

namespace Concrete\Core\Package\Packer;

use InvalidArgumentException;
use SplFileInfo;

class PackerFile
{
    /**
     * File type: other.
     *
     * @var int
     */
    const TYPE_OTHER = 0;

    /**
     * File type: source .pot translation file.
     *
     * @var int
     */
    const TYPE_TRANSLATIONS_POT = 1;

    /**
     * File type: source .po translation file.
     *
     * @var int
     */
    const TYPE_TRANSLATIONS_PO = 2;

    /**
     * File type: package source .svg icon file.
     *
     * @var int
     */
    const TYPE_SVGICON_PACKAGE = 3;

    /**
     * File type: block type source .svg icon file.
     *
     * @var int
     */
    const TYPE_SVGICON_BLOCKTYPE = 4;

    /**
     * File type: theme source .svg icon file.
     *
     * @var int
     */
    const TYPE_SVGICON_THEME = 5;

    /**
     * The absolute path to the actual file/directory (with directory separators normalized to '/', without trailing slashes).
     *
     * @var string
     */
    private $absolutePath;

    /**
     * The path to the file/directory relative to the package root directory (with directory separators normalized to '/', without trailing slashes).
     *
     * @var string
     */
    private $relativePath;

    /**
     * Is this a directory?
     *
     * @var bool
     */
    private $isDirectory;

    /**
     * Is this a file that has been modified?
     *
     * @var bool
     */
    private $isModified;

    /**
     * The name of the file/directory, without the path.
     *
     * @var string|null
     */
    private $basename;

    /**
     * The extension of the file, lower case without the leading dot.
     *
     * @var string|null
     */
    private $extension;

    /**
     * The type of the file (one of the TYPE_... constants).
     *
     * @var int|null
     */
    private $type;

    /**
     * Initialize the instance.
     *
     * @param string $absolutePath the absolute path to the actual file/directory (with directory separators normalized to '/', without trailing slashes)
     * @param string $relativePath the path to the file/directory relative to the package root directory
     * @param bool $isDirectory TRUE if it's a directory, FALSE otherwise
     * @param bool $isModified is this a file that has been modified?
     */
    protected function __construct($absolutePath, $relativePath, $isDirectory, $isModified)
    {
        $this->absolutePath = $absolutePath;
        $this->relativePath = $relativePath;
        $this->isDirectory = $isDirectory;
        $this->isModified = $isModified;
    }

    /**
     * Create a new instance of a file/directory as read from the source package directory (marking it as not changed).
     *
     * @param string $basePath the absolute path of the package root directory
     * @param \SplFileInfo $fileInfo the file/directory
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public static function fromSourceFileInfo($basePath, SplFileInfo $fileInfo)
    {
        $basePath = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $basePath), '/');
        $absolutePath = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $fileInfo->getPathname()), '/');
        $basePathLength = strlen($basePath) + 1;
        if (strlen($absolutePath) <= $basePathLength || strpos($absolutePath, $basePath . '/') !== 0) {
            throw new InvalidArgumentException();
        }

        return new static($absolutePath, substr($absolutePath, $basePathLength), $fileInfo->isDir(), false);
    }

    /**
     * Create a new instance of a file, representing a replacement for an existing original package file.
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $originalFile the original file that's being replaced
     * @param string $actualSourcePath the path to actual file that replaces the original file
     *
     * @throws \InvalidArgumentException
     *
     * @return \Concrete\Core\Package\Packer\PackerFile
     */
    public static function newChangedFile(PackerFile $originalFile, $actualSourcePath)
    {
        if ($originalFile->isDirectory()) {
            throw new InvalidArgumentException();
        }
        $actualSourcePath = str_replace(DIRECTORY_SEPARATOR, '/', $actualSourcePath);

        return new static($actualSourcePath, $originalFile->getRelativePath(), false, true);
    }

    /**
     * Create a new instance of a file, representing a new file added to the package directory.
     *
     * @param string $absolutePath the path to actual file that replaces the original file
     * @param string $relativePath the path to of the file relative to the package root directory
     */
    public static function newlyCreatedFile($absolutePath, $relativePath)
    {
        $absolutePath = str_replace(DIRECTORY_SEPARATOR, '/', $absolutePath);
        $relativePath = trim(str_replace(DIRECTORY_SEPARATOR, '/', $relativePath), '/');

        return new static($absolutePath, $relativePath, false, true);
    }

    /**
     * Get the absolute path to the actual file/directory (with directory separators normalized to '/', without trailing slashes).
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->absolutePath;
    }

    /**
     * Generate a new absolute path, with a new file extension.
     *
     * @param string $newExtension
     *
     * @return string
     */
    public function getAbsolutePathWithExtension($newExtension)
    {
        return $this->getPathWithExtension($this->getAbsolutePath(), $newExtension);
    }

    /**
     * Get the path to the file/directory relative to the package root directory (with directory separators normalized to '/', without trailing slashes).
     *
     * @return string
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * Generate a new relative path, with a new file extension.
     *
     * @param string $newExtension
     *
     * @return string
     */
    public function getRelativePathWithExtension($newExtension)
    {
        return $this->getPathWithExtension($this->getRelativePath(), $newExtension);
    }

    /**
     * Is this a directory?
     *
     * @return bool
     */
    public function isDirectory()
    {
        return $this->isDirectory;
    }

    /**
     * Is this a file that has been modified?
     *
     * @return bool
     */
    public function isModified()
    {
        return $this->isModified;
    }

    /**
     * The name of the file/directory, without the path.
     *
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
     * Get the extension of the file, lower case without the leading dot.
     *
     * Lower case, wihout dot.
     *
     * @return string
     */
    public function getExtension()
    {
        if ($this->extension === null) {
            $basename = $this->getBasename();
            $p = strrpos($basename, '.');
            $this->extension = $p === false ? '' : strtolower(substr($basename, $p + 1));
        }

        return $this->extension;
    }

    /**
     * Get the type of the file (one of the TYPE_... constants).
     *
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

    /**
     * Change the extension of a path.
     *
     * @param string $myPath
     * @param string $newExtension
     *
     * @return string
     */
    protected function getPathWithExtension($myPath, $newExtension)
    {
        $extension = $this->getExtension();
        $newExtension = ltrim((string) $newExtension, '.');
        if ($extension === '') {
            return $newExtension === '' ? $myPath : rtrim($myPath, '.') . '.' . $newExtension;
        }

        return substr($myPath, 0, -strlen($extension)) . $newExtension;
    }
}
