<?php

namespace Concrete\Core\Csv\Export;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Writer;

defined('C5_EXECUTE') or die('Access Denied.');

abstract class AbstractExporter
{
    /**
     * The CSV Writer instance.
     *
     * @var \League\Csv\Writer
     */
    private $writer;

    /**
     * The attribute category.
     *
     * @var \Concrete\Core\Attribute\Category\CategoryInterface|null
     */
    private $category;

    /**
     * The memoized attribute keys and controllers for the attribute category.
     *
     * @var array|null
     */
    private $attributeKeysAndControllers;

    /**
     * Unload doctrine entities every X ticks (0 for never).
     *
     * @var int
     */
    private $unloadDoctrineEveryTick = 0;

    /**
     * Remaining ticks before unloading Doctrine entities.
     *
     * @var int|null
     */
    private $ticksUntilUnload = null;

    /**
     * Initialize the instance.
     *
     * @param \League\Csv\Writer $writer the CSV Writer instance
     * @param \Concrete\Core\Attribute\Category\CategoryInterface|null $category the attribute category
     */
    protected function __construct(Writer $writer, CategoryInterface $category = null)
    {
        $this->setWriter($writer);
        if ($category !== null) {
            $this->setCategory($category);
        }
    }

    /**
     * Set the number of ticks after which doctrine entities should be unloaded (0 for never).
     *
     * @param int $value
     *
     * @return $this
     */
    public function setUnloadDoctrineEveryTick($value)
    {
        $this->unloadDoctrineEveryTick = max(0, (int) $value);
        $this->ticksUntilUnload = $this->unloadDoctrineEveryTick ?: null;

        return $this;
    }

    /**
     * Get the number of ticks after which doctrine entities should be unloaded (0 for never).
     *
     * @return int
     */
    public function getUnloadDoctrineEveryTick()
    {
        return $this->unloadDoctrineEveryTick;
    }

    /**
     * Add a tick (to be used to unload Doctrine entities).
     *
     * @return $this
     */
    public function tick()
    {
        if ($this->ticksUntilUnload !== null) {
            --$this->ticksUntilUnload;
            if ($this->ticksUntilUnload < 1) {
                $this->unloadDoctrineEntities();
                $this->ticksUntilUnload = $this->unloadDoctrineEveryTick;
            }
        }

        return $this;
    }

    /**
     * Insert the header row.
     *
     * @return $this
     */
    public function insertHeaders()
    {
        $this->writer->insertOne(iterator_to_array($this->projectHeaders()));

        return $this;
    }

    /**
     * Insert a row for a specific object instance.
     *
     * @param ObjectInterface $object
     *
     * @return $this
     */
    public function insertObject(ObjectInterface $object)
    {
        $this->writer->insertOne(iterator_to_array($this->projectObject($object)));

        return $this;
    }

    /**
     * Insert one row for every object in a database list.
     *
     * @param ItemList $list
     *
     * @return $this
     */
    public function insertList(ItemList $list)
    {
        $this->writer->insertAll($this->projectList($list));

        return $this;
    }

    /**
     * Get the list of fixed headers.
     *
     * @return string[]|\Generator
     */
    abstract protected function getStaticHeaders();

    /**
     * Get the list of fixed values of an object instance.
     *
     * @param ObjectInterface $object
     *
     * @return string[]|\Generator
     */
    abstract protected function getStaticFieldValues(ObjectInterface $object);

    /**
     * Override this method if the item returned by an ItemList is not already an ObjectInterface instance.
     *
     * @param ItemList $list The list that returned the result
     * @param mixed $listResult The value returned from the ItemList
     *
     * @return ObjectInterface
     */
    protected function getObjectFromListResult(ItemList $list, $listResult)
    {
        return $listResult;
    }

    /**
     * Set the CSV Writer instance.
     *
     * @param \League\Csv\Writer $writer
     *
     * @return $this
     */
    protected function setWriter(Writer $writer)
    {
        $this->writer = $writer;

        return $this;
    }

    /**
     * Get the CSV Writer instance.
     *
     * @return \League\Csv\Writer
     */
    protected function getWriter()
    {
        return $this->writer;
    }

    /**
     * Set the attribute category to be used to export the data.
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     *
     * @return $this
     */
    protected function setCategory(CategoryInterface $category)
    {
        $this->category = $category;
        $this->attributeKeysAndControllers = null;

        return $this;
    }

    /**
     * Get the attribute category to be used to export the data.
     *
     * @return \Concrete\Core\Attribute\Category\CategoryInterface|null
     */
    protected function getCategory()
    {
        return $this->category;
    }

    /**
     * A generator that returns all headers.
     *
     * @return string[]|\Generator
     */
    protected function projectHeaders()
    {
        foreach ($this->getStaticHeaders() as $header) {
            yield $header;
        }

        foreach ($this->getAttributeKeysAndControllers() as list($attributeKey, $attributeController)) {
            /* @var \Concrete\Core\Attribute\AttributeKeyInterface $attributeKey */
            /* @var \Concrete\Core\Attribute\Controller $attributeController */
            $handle = 'a:' . $attributeKey->getAttributeKeyHandle();
            if ($attributeController instanceof SimpleTextExportableAttributeInterface) {
                yield $handle;
            } elseif ($attributeController instanceof MulticolumnTextExportableAttributeInterface) {
                foreach ($attributeController->getAttributeTextRepresentationHeaders() as $subHeader) {
                    yield $handle . '[' . $subHeader . ']';
                }
            }
        }
    }

    /**
     * A generator that returns all fields of an object instance.
     *
     * @param ObjectInterface $object
     *
     * @return string[]|\Generator
     */
    protected function projectObject(ObjectInterface $object)
    {
        foreach ($this->getStaticFieldValues($object) as $value) {
            yield $value;
        }

        foreach ($this->getAttributeKeysAndControllers() as list($attributeKey, $attributeController)) {
            /* @var \Concrete\Core\Attribute\AttributeKeyInterface $attributeKey */
            /* @var \Concrete\Core\Attribute\Controller $attributeController */
            $value = $object->getAttributeValueObject($attributeKey, false);
            $attributeController->setAttributeValue($value);
            if ($attributeController instanceof SimpleTextExportableAttributeInterface) {
                yield $attributeController->getAttributeValueTextRepresentation();
            } elseif ($attributeController instanceof MulticolumnTextExportableAttributeInterface) {
                foreach ($attributeController->getAttributeValueTextRepresentation() as $part) {
                    yield $part;
                }
            }
        }
    }

    /**
     * A generator that returns all the rows for an object list.
     *
     * @param ItemList $list
     *
     * @return string[][]\Generator
     */
    protected function projectList(ItemList $list)
    {
        $sth = $list->deliverQueryObject()->execute();

        foreach ($sth as $row) {
            $listResult = $list->getResult($row);
            $object = $this->getObjectFromListResult($list, $listResult);
            yield iterator_to_array($this->projectObject($object));
            $this->tick();
        }
    }

    /**
     * Get a list the attribute keys and controllers for the current category.
     *
     * @return array
     */
    protected function getAttributeKeysAndControllers()
    {
        if ($this->attributeKeysAndControllers === null) {
            $list = [];
            $category = $this->getCategory();
            if ($category !== null) {
                foreach ($category->getList() as $attributeKey) {
                    $list[] = [$attributeKey, $attributeKey->getController()];
                }
            }
            $this->attributeKeysAndControllers = $list;
        }

        return $this->attributeKeysAndControllers;
    }

    /**
     * Unload every Doctrine entites, and reset the state of this instance.
     */
    protected function unloadDoctrineEntities()
    {
        $this->attributeKeysAndControllers = null;
        $app = Application::getFacadeApplication();
        $entityManager = $app->make(EntityManagerInterface::class);
        $entityManager->clear();
        $category = $this->getCategory();
        if ($category !== null) {
            $categoryClass = ClassUtils::getClass($category);
            if (!$entityManager->getMetadataFactory()->isTransient($categoryClass)) {
                $entityManager->merge($category);
            }
        }
    }
}
