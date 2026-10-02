<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol\AttributedItemListGenerator;

use Concrete\Core\File\Service\File as FileService;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Read the attribute keys from the CIF files (for instances that are not installed).
 */
final class CIFAttributeKeysProvider implements AttributeKeysProviderInterface
{
    /**
     * @var \Concrete\Core\File\Service\File
     */
    private $fileService;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var array<string, \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKey[]>|null
     */
    private $keys;

    /**
     * @param string $directory the directory containing the CIF files (it's scanned recursively)
     */
    public function __construct(FileService $fileService, string $directory)
    {
        $this->fileService = $fileService;
        $this->directory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $directory), '/');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKeysProviderInterface::getCategoryHandles()
     */
    public function getCategoryHandles(): array
    {
        return array_keys($this->getKeysByCategory());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKeysProviderInterface::getKeys()
     */
    public function getKeys(string $categoryHandle): array
    {
        return $this->getKeysByCategory()[$categoryHandle] ?? [];
    }

    /**
     * @return array<string, \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKey[]>
     */
    private function getKeysByCategory(): array
    {
        if ($this->keys === null) {
            $keys = [];
            foreach ($this->listXmlFiles($this->directory) as $file) {
                foreach ($this->readAttributeKeys($file) as $key) {
                    // The same attribute key may be defined in more than one file (for example in base/ and in upgrade/)
                    if (!isset($keys[$key->getCategoryHandle()][$key->getHandle()])) {
                        $keys[$key->getCategoryHandle()][$key->getHandle()] = $key;
                    }
                }
            }
            ksort($keys, SORT_STRING);
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
     * @return \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKey[]
     */
    private function readAttributeKeys(string $file): array
    {
        $result = [];
        try {
            $xml = new \SimpleXMLElement($file, 0, true);
            $elements = $xml->xpath('/concrete5-cif/attributekeys/attributekey') ?: [];
            // The attribute keys of the Express entities don't specify their category
            $expressElements = $xml->xpath('/concrete5-cif/expressentities/entity/attributekeys/attributekey') ?: [];
        } catch (\Throwable $_) {
            return $result;
        }
        foreach ($elements as $element) {
            $categoryHandle = (string) $element['category'];
            $handle = (string) $element['handle'];
            if ($categoryHandle === '' || $handle === '') {
                continue;
            }
            $result[] = new AttributeKey($categoryHandle, $handle, (string) $element['name'], $this->isSearchable($element));
        }
        foreach ($expressElements as $element) {
            $handle = (string) $element['handle'];
            if ($handle === '') {
                continue;
            }
            $result[] = new AttributeKey('express', $handle, (string) $element['name'], $this->isSearchable($element));
        }

        return $result;
    }

    private function isSearchable(\SimpleXMLElement $element): bool
    {
        return filter_var((string) $element['searchable'], FILTER_VALIDATE_BOOLEAN);
    }
}
