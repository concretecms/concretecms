<?php

namespace Concrete\Core\Search\Index;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;

/**
 * Default Search Index Manager
 * This manager allows indexing a type against all applicable registered indexes.
 * When searching, it returns the first result set found.
 *
 * @package Concrete\Core\Search\Index
 */
class DefaultManager implements IndexManagerInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** The type to use when you want to apply to all types */
    const TYPE_ALL = "-1";

    /**
     * @var array [ "TYPE" => [ $index1, $index2 ] ]
     */
    protected $indexes = [];

    protected $inflated = [];

    /**
     * Get the indexes for a type
     * @param string $type
     * @param bool $includeGlobal
     * @return \Concrete\Core\Search\Index\IndexInterface[]|\Concrete\Core\Search\Index\Iterator
     */
    public function getIndexes($type, $includeGlobal=true)
    {
        // Yield all indexes registered against this type
        if (isset($this->indexes[$type])) {
            foreach ($this->indexes[$type] as $index) {
                yield $type => $this->inflateIndex($index);
            }
        }

        $all = self::TYPE_ALL;
        if ($type !== $all && $includeGlobal) {
            // Yield all indexes registered against ALL types
            if (isset($this->indexes[$all])) {
                foreach ($this->indexes[$all] as $key => $index) {
                    yield $all => $this->inflateIndex($index);
                }
            }
        }
    }

    /**
     * Get the proper index from the stored value
     * @param $class
     * @return IndexInterface
     */
    protected function inflateIndex($class)
    {
        if ($class instanceof IndexInterface) {
            return $class;
        }

        if (!isset($this->inflated[$class])) {
            $this->inflated[$class] = $this->app->make($class);
        }

        return $this->inflated[$class];
    }

    /**
     * Get all indexes registered against this manager
     * @return \Generator
     */
    public function getAllIndexes()
    {
        foreach ($this->indexes as $type => $indexList) {
            // If we hit the "ALL" type, skip it for now
            if ($type == self::TYPE_ALL) {
                continue;
            }

            // Otherwise yield all indexes registered against this type
            foreach ($this->getIndexes($type, false) as $index) {
                yield $type => $index;
            }
        }

        foreach ($this->getIndexes(self::TYPE_ALL) as $index) {
            yield self::TYPE_ALL => $index;
        }
    }

    /**
     * Add an index to this manager
     * @param string $type The type to index. Use DefaultManager::TYPE_ALL to apply to all types.
     * @param IndexInterface|string $index
     */
    public function addIndex($type, $index)
    {
        if (!isset($this->indexes[$type])) {
            $this->indexes[$type] = [];
        }

        $this->indexes[$type][] = $index;
    }

    /**
     * Index an object
     * @param string $type
     * @param mixed $object
     * @return void
     */
    public function index($type, $object)
    {
        foreach ($this->getIndexes($type) as $index) {
            $index->index($object);
        }
    }

    /**
     * Forget an object
     * @param string $type
     * @param mixed $object
     * @return void
     */
    public function forget($type, $object)
    {
        foreach ($this->getIndexes($type) as $index) {
            $index->forget($object);
        }
    }

    /**
     * Clear out a type.
     * Passing DefaultManager::TYPE_ALL will clear out ALL types, not just types registered against ALL
     * @param string $type The type to clear
     */
    public function clear($type)
    {
        if ($type == self::TYPE_ALL) {
            $indexes = $this->getAllIndexes();
        } else {
            $indexes = $this->getIndexes($type);
        }

        foreach ($indexes as $index) {
            $index->clear();
        }
    }

}
