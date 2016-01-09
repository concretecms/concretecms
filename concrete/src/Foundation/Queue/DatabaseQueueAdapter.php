<?php
namespace Concrete\Core\Foundation\Queue;
use \ZendQueue\Queue as ZendQueue;
use \ZendQueue\Exception\RuntimeException;
use \ZendQueue\Message;

class DatabaseQueueAdapter extends \ZendQueue\Adapter\AbstractAdapter
{

    protected $db;

    public function __construct($options = array(), ZendQueue $queue = null)
    {
        $this->db = $options['connection'];
        parent::__construct($options, $queue);
    }

    public function isExists($name)
    {
        $id = 0;

        try {
            $id = $this->getQueueId($name);
        } catch (Exception $e) {
            return false;
        }

        return ($id > 0);
    }

    protected function getQueueId($name)
    {

        $r = $this->db->fetchAssoc('select queue_id from Queues where queue_name = ?', array($name));

        if ($r === null) {
            throw new RuntimeException('Queue does not exist: ' . $name);
        }

        $count = (int) $r['queue_id'];
        return $count;
    }


    public function create($name, $timeout = null)
    {
        if ($this->isExists($name)) {
            return false;
        }

        try {
            $this->db->insert('Queues', array(
                'queue_name' => $name,
                'timeout' => ($timeout === null) ? self::CREATE_TIMEOUT_DEFAULT : (int) $timeout
            ));
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return false;
    }

    public function delete($name)
    {
        $id = $this->getQueueId($name); // get primary key

        // if the queue does not exist then it must already be deleted.
        $r = $this->db->GetOne('select queue_id from Queues where queue_id = ?', array($id));
        if (!$r) {
            return false;
        }
        try {
            $this->db->delete('Queues', array('queue_id' => $id));
        } catch(\Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return true;
    }


    public function getQueues()
    {
        $r = $this->db->Execute('select queue_id, queue_name from Queues');
        $queues = array();
        while ($row = $r->FetchRow()) {
            $queues[$row['queue_name']] = $row['queue_id'];
        }

        $list = array_keys($queues);

        return $list;

    }

    public function count(ZendQueue $queue = null)
    {
        if ($queue === null) {
            $queue = $this->_queue;
        }
        $count = $this->db->GetOne('select count(*) from QueueMessages where queue_id = ?', array(
           $this->getQueueId($queue->getName())
        ));

        return (int) $count;
    }

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

        if (!$this->isExists($queue->getName())) {
            throw new RuntimeException('Queue does not exist:' . $queue->getName());
        }

        $msg = array();
        $msg['queue_id'] = $this->getQueueId($queue->getName());
        $msg['created'] = time();
        $msg['body'] = $message;
        $msg['md5'] = md5($message);

        try {
            $this->db->insert('QueueMessages', $msg);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $options = array(
            'queue' => $queue,
            'data'  => $msg,
        );

        $classname = $queue->getMessageClass();
        return new $classname($options);
    }

    public function receive($maxMessages = null, $timeout = null, ZendQueue $queue = null)
    {
        if ($maxMessages === null) {
            $maxMessages = 1;
        }
        if ($timeout === null) {
            $timeout = self::RECEIVE_TIMEOUT_DEFAULT;
        }
        if ($queue === null) {
            $queue = $this->_queue;
        }

        $msgs      = array();
        $microtime = microtime(true); // cache microtime

        // start transaction handling
        try {
            if ( $maxMessages > 0 ) { // ZF-7666 LIMIT 0 clause not included.
                $this->db->beginTransaction();
                $statement = $this->db->prepare('select * from QueueMessages where queue_id = ? and handle is null or timeout + ' . (int) $timeout . ' < ' . (int) $microtime . ' limit ' . $maxMessages . ' for update');
                $statement->bindValue(1, $this->getQueueId($queue->getName()));
                $r = $statement->execute();

                foreach ($statement->fetchAll() as $data) {
                    // setup our changes to the message
                    $data['handle'] = md5(uniqid(rand(), true));

                    // update the database
                    $count = $this->db->executeUpdate('update QueueMessages set handle = ?, timeout = ? where message_id = ? and (handle is null or timeout + ' . (int)$timeout . ' < ' . (int) $microtime . ')',
                        array($data['handle'], $microtime, $data['message_id']));

                    // we check count to make sure no other thread has gotten
                    // the rows after our select, but before our update.
                    if ($count > 0) {
                        $msgs[] = $data;
                    }
                }
                $this->db->commit();
            }
        } catch (\Exception $e) {
            $this->db->rollback();
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $options = array(
            'queue'        => $queue,
            'data'         => $msgs,
            'messageClass' => $queue->getMessageClass(),
        );

        $classname = $queue->getMessageSetClass();
        return new $classname($options);
    }

    public function deleteMessage(Message $message)
    {
        if ($this->db->delete('QueueMessages', array('handle' => $message->handle))) {
            return true;
        }

        return false;
    }


    public function getCapabilities()
    {
        return array(
            'create'        => true,
            'delete'        => true,
            'send'          => true,
            'receive'       => true,
            'deleteMessage' => true,
            'getQueues'     => true,
            'count'         => true,
            'isExists'      => true,
        );
    }
}