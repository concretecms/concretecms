<?php
namespace Concrete\Core\Foundation\Queue;

use Loader;
use Database;
use ZendQueue\Queue as ZendQueue;

class Queue
{
    public static function get($name, $additionalConfig = array())
    {
        $config = array(
            'name' => $name,
        );

        $db = Database::get();

        $adapterOptions = array(
            'connection' => $db,
        );

        $adapter = new DatabaseQueueAdapter($adapterOptions);
        $config = array_merge($config, $additionalConfig);

        return new ZendQueue($adapter, $config);
    }

    public static function exists($name)
    {
        // probably should use the Zend Queue for this but it's just such overhead for a quick
        // DB call.
        $db = Loader::db();
        $r = $db->GetOne('select queue_id from Queues where queue_name = ?', array($name));

        return $r > 0;

        /*
        $q = Queue::get($name);
        if ($q->count() > 0) {
            return true;
        } else {
            $q->deleteQueue($name);
            return false;
        }
        */
    }
}
