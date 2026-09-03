<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol;

use Concrete\Core\File\Service\File as FileService;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * List the classes defined in a directory tree (with PSR-4 compliant file names).
 */
final class ClassLister
{
    /**
     * @var \Concrete\Core\File\Service\File
     */
    private $fileService;

    /**
     * @var string
     */
    private $namespacePrefix;

    /**
     * @var string
     */
    private $directory;

    /**
     * The fully-qualified names of the classes (NULL if not yet listed).
     *
     * @var string[]|null
     */
    private $classNames;

    /**
     * @param string $namespacePrefix the namespace of the classes in $directory (without leading/trailing backslashes)
     * @param string $directory the directory containing the classes (it will be scanned recursively)
     */
    public function __construct(FileService $fileService, string $namespacePrefix, string $directory)
    {
        $this->fileService = $fileService;
        $this->namespacePrefix = trim($namespacePrefix, '\\');
        $this->directory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $directory), '/');
    }

    /**
     * Get the fully-qualified names of the classes (and interfaces, and traits) that can actually be loaded.
     *
     * @return string[]
     */
    public function getClassNames(): array
    {
        if ($this->classNames === null) {
            $this->classNames = $this->listClassNames($this->namespacePrefix, $this->directory);
        }

        return $this->classNames;
    }

    /**
     * @return string[]
     */
    private function listClassNames(string $namespacePrefix, string $directory): array
    {
        $result = [];
        $matches = null;
        foreach ($this->fileService->getDirectoryContents($directory) as $name) {
            $fullPath = "{$directory}/{$name}";
            if (is_dir($fullPath)) {
                $result = array_merge($result, $this->listClassNames("{$namespacePrefix}\\{$name}", $fullPath));
            } elseif (preg_match('/^(\w.*)\.php$/i', $name, $matches)) {
                $className = "{$namespacePrefix}\\{$matches[1]}";
                if ($this->classExists($className)) {
                    $result[] = $className;
                }
            }
        }

        return $result;
    }

    private function classExists(string $className): bool
    {
        switch ($className) {
            case 'Concrete\Core\Support\__IDE_SYMBOLS__':
                // Generated file containing fake classes
                return false;
        }
        if (strpos($className, 'Concrete\Core\Support\CodingStyle\\') === 0) {
            // These classes require php-cs-fixer, which is not a dependency
            return false;
        }
        try {
            return class_exists($className) || interface_exists($className) || trait_exists($className);
        } catch (\Throwable $_) {
            // For example, a class extending a class defined in a library that's not installed
            return false;
        }
    }
}
