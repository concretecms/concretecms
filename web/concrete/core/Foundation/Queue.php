<?php
namespace Concrete\Core\Foundation;
use Loader;

class Queue {

	public static function get($name, $additionalConfig = array()) {
	
		$type = 'pdo_mysql';
		if (!extension_loaded('pdo_mysql')) {
			$type = 'mysql';
		}

		$config = array(
		'name' => $name,
		'driverOptions' => array(
		'host'     => DB_SERVER,
		'username' => DB_USERNAME,
		'password' => DB_PASSWORD,
		'dbname'   => DB_DATABASE,
		'type'     => $type
		)
		);

		$config = array_merge($config, $additionalConfig);
		return new Zend_Queue('Concrete5', $config);
	}

	public static function exists($name) {
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