<?php

namespace Concrete\Core\Search\Index;

interface IndexManagerInterface
{

    /**
     * Get the indexes for a type
     * @param string $type
     * @return Iterator
     */
    public function getIndexes($type);

    /**
     * Index an object
     * @param $type
     * @param $object
     * @return void
     */
    public function index($type, $object);

    /**
     * Forget an object
     * @param string $type
     * @param mixed $object
     * @return void
     */
    public function forget($type, $object);


    /**
     * Clear out a type.
     * @param string $type The type to clear
     */
    public function clear($type);

}
