<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol\CheckerGenerator;

use Concrete\Core\File\Service\File as FileService;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Read the permission key categories and permission keys from the CIF (Content Import Format) XML files
 * (for Concrete instances that are not installed).
 */
final class CIFPermissionKeysProvider implements PermissionKeysProviderInterface
{
    /**
     * @var \Concrete\Core\File\Service\File
     */
    private $fileService;

    /**
     * The directory containing the CIF files (it will be scanned recursively).
     *
     * @var string
     */
    private $directory;

    /**
     * The permission keys, indexed by the category handle.
     * NULL if not yet loaded.
     *
     * @var array<string, \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKey[]>|null
     */
    private $keys;

    /**
     * @param string $directory the directory containing the CIF files (it will be scanned recursively)
     */
    public function __construct(FileService $fileService, string $directory)
    {
        $this->fileService = $fileService;
        $this->directory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $directory), '/');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKeysProviderInterface::getCategoryHandles()
     */
    public function getCategoryHandles(): array
    {
        return array_keys($this->getKeysByCategory());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKeysProviderInterface::getKeys()
     */
    public function getKeys(string $categoryHandle): array
    {
        return $this->getKeysByCategory()[$categoryHandle] ?? [];
    }

    /**
     * @return array<string, \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKey[]>
     */
    private function getKeysByCategory(): array
    {
        if ($this->keys === null) {
            $keys = [];
            foreach ($this->listXmlFiles($this->directory) as $file) {
                foreach ($this->readPermissionKeys($file) as $key) {
                    // The same permission key may be defined in more than one file (for example in base/ and in upgrade/)
                    if (!isset($keys[$key->getCategoryHandle()][$key->getHandle()])) {
                        $keys[$key->getCategoryHandle()][$key->getHandle()] = $key;
                    }
                }
            }
            $this->keys = array_map('array_values', $keys);
        }

        return $this->keys;
    }

    /**
     * @return string[]
     */
    private function listXmlFiles(string $directory): array
    {
        $files = [];
        $subDirectories = [];
        foreach ($this->fileService->getDirectoryContents($directory) as $name) {
            $path = "{$directory}/{$name}";
            if (is_dir($path)) {
                $subDirectories[] = $path;
            } elseif (preg_match('/\.xml$/i', $name)) {
                $files[] = $path;
            }
        }
        sort($files, SORT_STRING);
        sort($subDirectories, SORT_STRING);
        foreach ($subDirectories as $subDirectory) {
            $files = array_merge($files, $this->listXmlFiles($subDirectory));
        }

        return $files;
    }

    /**
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKey[]
     */
    private function readPermissionKeys(string $file): array
    {
        $result = [];
        try {
            $xml = new \SimpleXMLElement($file, 0, true);
            $elements = $xml->xpath('/concrete5-cif/permissionkeys/permissionkey') ?: [];
        } catch (\Throwable $_) {
            return $result;
        }
        foreach ($elements as $element) {
            $categoryHandle = (string) $element['category'];
            $handle = (string) $element['handle'];
            if ($categoryHandle === '' || $handle === '') {
                continue;
            }
            $result[] = new PermissionKey($categoryHandle, $handle, (string) $element['name'], (string) $element['description']);
        }

        return $result;
    }
}
