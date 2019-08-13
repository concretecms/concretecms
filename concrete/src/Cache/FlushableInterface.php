<?php

namespace Concrete\Core\Cache;

/**
 * A cache store that has the ability to be flushed
 * @since 8.2.0
 */
interface FlushableInterface
{

    /**
     * Removes all values from the cache.
     */
    public function flush();
}
