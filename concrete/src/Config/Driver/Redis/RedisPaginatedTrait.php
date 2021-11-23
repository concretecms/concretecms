<?php

namespace Concrete\Core\Config\Driver\Redis;

use Redis;

trait RedisPaginatedTrait
{

    /**
     * Scan for a specific key pattern
     *
     * @param Redis $redis
     * @param string $pattern The pattern to search for ex: `foo`, `*`, `foo.*`
     * @return \Generator|string[] A list of keys that match the pattern
     */
    protected function paginatedScan(Redis $redis, $pattern)
    {
        $i = 0;
        do {
            $keys = $redis->scan($i, 'cfg=' . $pattern, 100);

            if ($keys) {
                // Remove the prefix
                $keys = array_map(static function ($key) {
                    return substr($key, 4);
                }, $keys);

                foreach ($keys as $key) {
                    yield $key;
                }
            }

        } while ($keys);
    }

    /**
     * Get the keys and values matching a pattern
     *
     * @param Redis $redis
     * @param string $pattern The pattern to search for ex: `foo`, `*`, `foo.*`
     * @return \Generator|mixed[] A list of key => value results
     */
    protected function paginatedScanValues(Redis $redis, $pattern)
    {
        $batchSize = 50;
        $batch = [];
        foreach ($this->paginatedScan($redis, $pattern) as $key) {
            $batch[] = $key;
            if (count($batch) >= $batchSize) {

                // Load in values
                $values = array_combine($batch, $redis->mget($batch));

                // Yield them out
                foreach ($values as $valueKey => $value) {
                    yield $valueKey => $value;
                }

                // Reset batch
                $batch = [];
            }
        }

        if ($batch) {
            // Load in values
            $values = array_combine($batch, $redis->mget($batch));

            // Return any leftover batched items
            foreach ($values as $valueKey => $value) {
                yield $valueKey => $value;
            }
        }
    }
}
