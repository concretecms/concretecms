<?php

namespace Concrete\Tests\Foundation;

use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Queue;

class DatabaseQueueAdapterTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'Queues',
        'QueueMessages',
    ];

    public function testDataIntegrityBetweenQueues()
    {
        $q1 = Queue::get('q1', ['timeout' => 1]);
        $q2 = Queue::get('q2', ['timeout' => 1]);

        $idQuery = 'SELECT queue_id FROM Queues WHERE queue_name =?';
        $q1Id = static::$connection->fetchColumn($idQuery, ['q1']);
        $q2Id = static::$connection->fetchColumn($idQuery, ['q2']);

        // Push same messages to both queues
        for ($i = 0; $i < 3; $i++) {
            $q1->send("{$i}");
            $q2->send("{$i}");
        }

        // Receive and delete a single item from the first queue, just to test
        // that the first queue works correctly.
        foreach ($q1->receive(1) as $msg) {
            $q1->deleteMessage($msg);
        }

        // Receive the rest of the items from the first queue but do not delete
        // them yet. This leaves them hanging in the database with a `timeout`
        // value created for the items.
        $msgs1 = [];
        foreach ($q1->receive(4) as $msg) {
            $msgs1[] = $msg->message_id;

            // Make sure the message is from the first queue
            $this->assertEquals($q1Id, $msg->queue_id);
        }
        $this->assertCount(2, $msgs1);

        // Make sure when fetching the second queue's items, we have already
        // reached the timeout value.
        sleep(2);

        // Load the messages from the second queue to test they only contain the
        // data they should contain and are not mixed up with `q1`.
        $msgs2 = [];
        foreach ($q2->receive(10) as $msg) {
            $msgs2[] = $msg->message_id;

            // Make sure the message is from the second queue
            $this->assertEquals($q2Id, $msg->queue_id);
        }
        $this->assertCount(3, $msgs2);

        $q1->deleteQueue();
        $q2->deleteQueue();
    }
}
