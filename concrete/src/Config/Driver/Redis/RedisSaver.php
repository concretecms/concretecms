<?php

namespace Concrete\Core\Config\Driver\Redis;

use Concrete\Core\Config\SaverInterface;
use Illuminate\Support\Arr;
use Redis;

class RedisSaver implements SaverInterface
{

    use RedisPaginatedTrait;

    /**
     * @var Redis
     */
    protected $connection;

    public function __construct(Redis $redis)
    {
        $this->connection = $redis;
    }

    /**
     * Save config item.
     *
     * @param string $item
     * @param string $value
     * @param string $environment
     * @param string $group
     * @param string|null $namespace
     *
     * @return bool
     */
    public function save($item, $value, $environment, $group, $namespace = null)
    {
        // First we gotta clear the item
        $key = "{$namespace}::{$group}" . ($item ? ".{$item}" : '');
        $deleteKeys = [$key];
        foreach ($this->paginatedScan($this->connection, $key . '.*') as $key) {
            $deleteKeys[] = $key;
        }

        if ($deleteKeys) {
            $this->connection->del($deleteKeys);
        }

        // Now we can convert the value into a flat array and save each key
        $valueList = $this->flattenValue($namespace, $group, $item, $value);
        return $this->connection->mset($valueList);
    }

    /**
     * Flatten a given value into a list of keys => serialized values
     * ['a' => ['b' => 'c']] would become ['a.b' => 's:1:"c";']
     *
     * @param $namespace
     * @param $group
     * @param $item
     * @param mixed $value
     *
     * @return mixed[]
     */
    protected function flattenValue($namespace, $group, $item, $value)
    {
        $prefix = "{$namespace}::{$group}" . ($item ? ".{$item}" : '');

        if (!is_array($value)) {
            return [$prefix => serialize($value)];
        }

        return array_map('serialize', Arr::dot($value, $prefix . '.'));
    }
}
