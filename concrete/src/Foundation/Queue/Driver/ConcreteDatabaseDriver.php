<?php

namespace Concrete\Core\Foundation\Queue\Driver;

use Bernard\Driver\DoctrineDriver;
use Doctrine\DBAL\Connection;

/**
 * Driver supporting Concrete's DBAL implementation
 *
 */
class ConcreteDatabaseDriver extends DoctrineDriver
{

    /**
     * {@inheritdoc}
     */
    public function listQueues()
    {
        $statement = $this->connection->prepare('SELECT name FROM Queues');
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * {@inheritdoc}
     */
    public function createQueue($queueName)
    {
        try {
            $this->connection->insert('Queues', ['name' => $queueName]);
        } catch (\Exception $e) {
            // Because SQL server does not support a portable INSERT ON IGNORE syntax
            // this ignores error based on primary key.
        }
    }

    /**
     * {@inheritdoc}
     */
    public function countMessages($queueName)
    {
        $query = 'SELECT COUNT(id) FROM QueueMessages WHERE queue = :queue AND visible = :visible';

        return (integer) $this->connection->fetchColumn($query, [
            'queue' => $queueName,
            'visible' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function pushMessage($queueName, $message)
    {
        $types = ['string', 'string', 'datetime'];
        $data = [
            'queue' => $queueName,
            'message' => $message,
            'sentAt' => new \DateTime(),
        ];

        $this->createQueue($queueName);
        $this->connection->insert('QueueMessages', $data, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function acknowledgeMessage($queueName, $receipt)
    {
        $this->connection->delete('QueueMessages', ['id' => $receipt, 'queue' => $queueName]);
    }

    /**
     * {@inheritdoc}
     */
    public function peekQueue($queueName, $index = 0, $limit = 20)
    {
        $parameters = [$queueName, $limit, $index];
        $types = ['string', 'integer', 'integer'];

        $query = 'SELECT message FROM QueueMessages WHERE queue = ? ORDER BY sentAt LIMIT ? OFFSET ?';

        return $this
            ->connection
            ->executeQuery($query, $parameters, $types)
            ->fetchAll(\PDO::FETCH_COLUMN)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function removeQueue($queueName)
    {
        $this->connection->delete('QueueMessages', ['queue' => $queueName]);
        $this->connection->delete('Queues', ['name' => $queueName]);
    }

    /**
     * Execute the actual query and process the response
     *
     * @param string $queueName
     *
     * @return array|null
     */
    protected function doPopMessage($queueName)
    {
        $query = 'SELECT id, message FROM QueueMessages
                  WHERE queue = :queue AND visible = :visible
                  ORDER BY sentAt LIMIT 1 ' . $this->connection->getDatabasePlatform()->getForUpdateSql();

        list($id, $message) = $this->connection->fetchArray($query, [
            'queue' => $queueName,
            'visible' => true,
        ]);

        if ($id) {
            $this->connection->update('QueueMessages', ['visible' => 0], compact('id'));

            return [$message, $id];
        }
    }
}
