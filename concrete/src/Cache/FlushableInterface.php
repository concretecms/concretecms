<?php

namespace Concrete\Core\Cache;

/**
 * A cache store that has the ability to be flushed
 */
interface FlushableInterface
{

    /**
     * Removes all values from the cache.
     */
    public function flush();
}
