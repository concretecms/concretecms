<?php

namespace Concrete\Core\Search\Index;

use Concrete\Core\Search\Index\Driver\IndexingDriverInterface;

/**
 * Interface IndexInterface
 * @package Concrete\Core\Search\Index
 * @since 8.1.0
 */
interface IndexInterface extends IndexingDriverInterface
{

    /**
     * Clear out all indexed items
     * @return void
     */
    public function clear();

}
