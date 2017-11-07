<?php

namespace Concrete\Core\Foundation\Queue;

use Concrete\Core\Database\Connection\Connection;
use ZendQueue\Queue as ZendQueue;

class QueueService
{
    /**
     * The database connection.
     *
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * Initialize the instance.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get a queue by name.
     *
     * @param string $name The queue name
     * @param array $additionalConfig additional configuration to be passed to the Zend Queue
     *
     * @return \ZendQueue\Queue
     */
    public function get($name, array $additionalConfig = [])
    {
        $adapter = new DatabaseQueueAdapter([
            'connection' => $this->connection,
        ]);
        $config = array_merge(
            [
                'name' => $name,
            ],
            $additionalConfig
        );

        return new ZendQueue($adapter, $config);
    }

    /**
     * Check if a queue with a specific name exists.
     *
     * @param string $name The queue name
     *
     * @return bool
     */
    public function exists($name)
    {
        $id = $this->connection->fetchColumn('select queue_id from Queues where queue_name = ? limit 1', [$name]);

        return (bool) $id;
    }
}
