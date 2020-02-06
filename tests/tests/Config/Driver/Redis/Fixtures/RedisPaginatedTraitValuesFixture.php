<?php

namespace Concrete\Tests\Config\Driver\Redis\Fixtures;

use Concrete\Core\Config\Driver\Redis\RedisPaginatedTrait;
use Redis;

class RedisPaginatedTraitValuesFixture
{

    use RedisPaginatedTrait;

    protected $scanMock;

    public function __construct($scanMock)
    {
        $this->scanMock = $scanMock;
    }

    /**
     * Scan for a specific key pattern
     *
     * @param Redis $redis
     * @param string $pattern The pattern to search for ex: `foo`, `*`, `foo.*`
     * @return \Generator|string[] A list of keys that match the pattern
     */
    protected function paginatedScan(Redis $redis, $pattern)
    {
        $scanMock = $this->scanMock;
        foreach ($scanMock($redis, $pattern) as $key => $value) {
            yield $key => $value;
        }
    }

}
