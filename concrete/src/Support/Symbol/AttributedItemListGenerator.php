<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Attribute\Key\ExpressKey;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\Key\SiteKey;
use Concrete\Core\Attribute\Key\SiteTypeKey;
use Concrete\Core\Attribute\Key\UserKey;
use Concrete\Core\Express\EntryList;
use Concrete\Core\File\Service\File as FileService;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKeysProviderInterface;
use Concrete\Core\Support\Symbol\AttributedItemListGenerator\CIFAttributeKeysProvider;
use Concrete\Core\Support\Symbol\AttributedItemListGenerator\DatabaseAttributeKeysProvider;
use Concrete\Core\Support\Symbol\CheckerGenerator\Method;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Describe the filterByXxx() and sortByXxx() methods that the attributed item lists handle with __call(): one for every attribute key of the category of the list.
 */
final class AttributedItemListGenerator
{
    /**
     * The attribute key categories handled by the legacy attribute key classes returned by AttributedItemList::getAttributeKeyClassName().
     *
     * @var array<string, string>
     */
    private const KEY_CLASS_CATEGORIES = [
        CollectionKey::class => 'collection',
        EventKey::class => 'event',
        ExpressKey::class => 'express',
        FileKey::class => 'file',
        SiteKey::class => 'site',
        SiteTypeKey::class => 'site_type',
        UserKey::class => 'user',
    ];

    /**
     * @var \Concrete\Core\Support\Symbol\ClassLister
     */
    private $classLister;

    /**
     * @var \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKeysProviderInterface
     */
    private $attributeKeysProvider;

    /**
     * @var array<string, \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]>|null
     */
    private $listClassMethods;

    /**
     * @param bool $isInstalled is Concrete installed? If so, the attribute keys are read from the database, otherwise from the CIF files of the core
     * @param \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKeysProviderInterface|null $attributeKeysProvider a custom provider of the attribute keys (if NULL, we'll use the default one depending on $isInstalled)
     * @param \Concrete\Core\Support\Symbol\ClassLister|null $classLister the lister of the core classes (if NULL, we'll create a new one)
     */
    public function __construct(FileService $fileService, bool $isInstalled, ?AttributeKeysProviderInterface $attributeKeysProvider = null, ?ClassLister $classLister = null)
    {
        if ($attributeKeysProvider === null) {
            $attributeKeysProvider = $isInstalled ? new DatabaseAttributeKeysProvider(app(EntityManagerInterface::class)) : new CIFAttributeKeysProvider($fileService, DIR_BASE_CORE . '/config/install');
        }
        $this->attributeKeysProvider = $attributeKeysProvider;
        $this->classLister = $classLister ?? new ClassLister($fileService, 'Concrete\Core', DIR_BASE_CORE . '/' . DIRNAME_CLASSES);
    }

    public function getAttributeKeysProvider(): AttributeKeysProviderInterface
    {
        return $this->attributeKeysProvider;
    }

    /**
     * Get the methods handled by __call() of the attributed item lists.
     *
     * @return array<string, \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]> array keys are the fully-qualified names of the list classes
     */
    public function getListClassMethods(): array
    {
        if ($this->listClassMethods === null) {
            $categoryHandles = [];
            foreach ($this->classLister->getClassNames() as $className) {
                if (!is_subclass_of($className, AttributedItemList::class)) {
                    continue;
                }
                $class = new \ReflectionClass($className);
                if ($class->isAbstract()) {
                    continue;
                }
                $categoryHandle = $this->getCategoryHandle($class);
                if ($categoryHandle !== null) {
                    $categoryHandles[$class->getName()] = $categoryHandle;
                }
            }
            $result = [];
            foreach ($categoryHandles as $className => $categoryHandle) {
                // The methods are inherited: describe them in the topmost class of the hierarchy only
                for ($parent = get_parent_class($className); $parent !== false; $parent = get_parent_class($parent)) {
                    if (($categoryHandles[$parent] ?? null) === $categoryHandle) {
                        continue 2;
                    }
                }
                $methods = [];
                foreach ($this->attributeKeysProvider->getKeys($categoryHandle) as $key) {
                    // Only the searchable attribute keys have a column in the search index tables used by the item lists
                    if (!$key->isSearchable()) {
                        continue;
                    }
                    $name = $key->getName() === '' ? $key->getHandle() : $key->getName();
                    $camelcased = camelcase($key->getHandle());
                    $method = new Method('filterBy' . $camelcased, '$value, $comparison = \'=\'');
                    $method
                        ->setReturnType('void')
                        ->addDescription(sprintf("Filter the results by the '%s' attribute (%s).", $name, $key->getHandle()))
                    ;
                    $methods[] = $method;
                    $method = new Method('sortBy' . $camelcased, '$direction = \'asc\'');
                    $method
                        ->setReturnType('void')
                        ->addDescription(sprintf("Sort the results by the '%s' attribute (%s).", $name, $key->getHandle()))
                    ;
                    $methods[] = $method;
                }
                if ($methods !== []) {
                    $result[$className] = $methods;
                }
            }
            ksort($result, SORT_STRING);
            $this->listClassMethods = $result;
        }

        return $this->listClassMethods;
    }

    /**
     * Render the classes describing the methods of the attributed item lists that are handled by __call().
     *
     * @return array<string, string[]> array keys are the fully-qualified names of the list classes, array values are the lines
     */
    public function renderClassesLines(string $padding = '    '): array
    {
        $result = [];
        foreach ($this->getListClassMethods() as $className => $methods) {
            $p = strrpos($className, '\\');
            $result[$className] = CheckerGenerator::renderMethodsClassLines($p === false ? $className : substr($className, $p + 1), $methods, $padding);
        }

        return $result;
    }

    /**
     * Get the handle of the attribute key category handled by an attributed item list.
     */
    private function getCategoryHandle(\ReflectionClass $class): ?string
    {
        if ($class->getName() === EntryList::class || $class->isSubclassOf(EntryList::class)) {
            // The Express entry lists use the attribute key category of their entity
            return 'express';
        }
        try {
            $method = $class->getMethod('getAttributeKeyClassName');
            if (PHP_VERSION_ID < 80100) {
                // Required to invoke protected methods before PHP 8.1 (and deprecated since PHP 8.5)
                $method->setAccessible(true);
            }
            $keyClassName = $method->invoke($class->newInstanceWithoutConstructor());
        } catch (\Throwable $_) {
            return null;
        }
        if (!is_string($keyClassName)) {
            return null;
        }

        return self::KEY_CLASS_CATEGORIES[ltrim($keyClassName, '\\')] ?? null;
    }
}
