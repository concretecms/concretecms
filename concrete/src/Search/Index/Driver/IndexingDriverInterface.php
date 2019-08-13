<?php

namespace Concrete\Core\Search\Index\Driver;

/**
 * @since 8.1.0
 */
interface IndexingDriverInterface
{

    /**
     * Add an object to the index
     * @param mixed $object Object to index
     * @return bool Success or fail
     */
    public function index($object);

    /**
     * Remove an object from the index
     * @param mixed $object Object to forget
     * @return bool Success or fail
     */
    public function forget($object);

}
