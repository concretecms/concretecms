<?php
namespace Concrete\Core\Foundation\Queue;

use Exception;
use ZendQueue\Exception\RuntimeException;
use ZendQueue\Message;
use ZendQueue\Queue as ZendQueue;

class DatabaseQueueAdapter extends \ZendQueue\Adapter\AbstractAdapter
{
    /**
     * The connection to the current database.
     *
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $db;

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::__construct()
     */
    public function __construct($options = [], ZendQueue $queue = null)
    {
        $this->db = $options['connection'];
        parent::__construct($options, $queue);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::isExists()
     */
    public function isExists($name)
    {
        try {
            $this->getQueueId($name);
            $result = true;
        } catch (RuntimeException $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get the identifier of a queue given its name.
     *
     * @param string $name The name of a queue
     *
     * @throws RuntimeException Throws a RuntimeException if the queue does not exist
     *
     * @return int
     */
    protected function getQueueId($name)
    {
        $r = $this->db->fetchColumn('select queue_id from Queues where queue_name = ? limit 1', [$name]);

        if ($r === false) {
            throw new RuntimeException(t('Queue does not exist: %s', $name));
        }

        $count = (int) $r;

        return $count;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::create()
     */
    public function create($name, $timeout = null)
    {
        if ($this->isExists($name)) {
            return false;
        }

        try {
            $this->db->insert('Queues', [
                'queue_name' => $name,
                'timeout' => ($timeout === null) ? self::CREATE_TIMEOUT_DEFAULT : (int) $timeout,
            ]);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::delete()
     */
    public function delete($name)
    {
        try {
            $id = $this->getQueueId($name);
        } catch (RuntimeException $e) {
            return false;
        }

        try {
            $this->db->delete('Queues', ['queue_id' => $id]);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::getQueues()
     */
    public function getQueues()
    {
        $r = $this->db->executeQuery('select queue_id, queue_name from Queues');
        $queues = [];
        while ($row = $r->fetch()) {
            $queues[$row['queue_name']] = (int) $row['queue_id'];
        }

        $list = array_keys($queues);

        return $list;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::count()
     */
    public function count(ZendQueue $queue = null)
    {
        if ($queue === null) {
            $queue = $this->_queue;
        }
        $queueName = $queue->getName();
        $queueId = $this->getQueueId($queueName);
        $count = $this->db->fetchColumn('select count(*) from QueueMessages where queue_id = ?', [$queueId]);

        return (int) $count;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::send()
     */
    public function send($message, ZendQueue $queue = null)
    {
        if ($queue === null) {
            $queue = $this->_queue;
        }

        if (is_scalar($message)) {
            $message = (string) $message;
        }
        if (is_string($message)) {
            $message = trim($message);
        }

        $queueId = $this->getQueueId($queue->getName());

        $msg = [
            'queue_id' => $queueId,
            'created' => time(),
            'body' => $message,
            'md5' => md5($message),
        ];
        try {
            $this->db->insert('QueueMessages', $msg);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $options = [
            'queue' => $queue,
            'data' => $msg,
        ];

        $classname = $queue->getMessageClass();

        return new $classname($options);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::receive()
     */
    public function receive($maxMessages = null, $timeout = null, ZendQueue $queue = null)
    {
        $maxMessages = $maxMessages === null ? 1 : max(1, (int) $maxMessages);
        $timeout = (int) ($timeout === null ? self::RECEIVE_TIMEOUT_DEFAULT : $timeout);
        if ($queue === null) {
            $queue = $this->_queue;
        }
        $queueId = $this->getQueueId($queue->getName());

        $msgs = [];
        $microtime = microtime(true); // cache microtime
        $microtimeInt = (int) $microtime;

        // start transaction handling
        try {
            if ($maxMessages > 0) { // ZF-7666 LIMIT 0 clause not included.
                $this->db->beginTransaction();
                $statement = $this->db->prepare("
                    select *
                    from QueueMessages
                    where queue_id = ? and handle is null or timeout + {$timeout} < {$microtimeInt}
                    limit {$maxMessages}
                    for update
                ");
                $statement->bindValue(1, $queueId);
                $r = $statement->execute();

                foreach ($statement->fetchAll() as $data) {
                    // setup our changes to the message
                    $data['handle'] = md5(uniqid(rand(), true));

                    // update the database
                    $count = $this->db->executeUpdate(
                        "
                            update QueueMessages
                            set handle = ?, timeout = ?
                            where message_id = ? and (handle is null or timeout + {$timeout} < {$microtimeInt})
                        ",
                        [$data['handle'], $microtime, $data['message_id']]
                    );

                    // we check count to make sure no other thread has gotten
                    // the rows after our select, but before our update.
                    if ($count > 0) {
                        $msgs[] = $data;
                    }
                }
                $this->db->commit();
            }
        } catch (Exception $e) {
            $this->db->rollback();
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $options = [
            'queue' => $queue,
            'data' => $msgs,
            'messageClass' => $queue->getMessageClass(),
        ];

        $classname = $queue->getMessageSetClass();

        return new $classname($options);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::deleteMessage()
     */
    public function deleteMessage(Message $message)
    {
        if ($this->db->delete('QueueMessages', ['handle' => $message->handle])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ZendQueue\Adapter::getCapabilities()
     */
    public function getCapabilities()
    {
        return [
            'create' => true,
            'delete' => true,
            'send' => true,
            'receive' => true,
            'deleteMessage' => true,
            'getQueues' => true,
            'count' => true,
            'isExists' => true,
        ];
    }
}
