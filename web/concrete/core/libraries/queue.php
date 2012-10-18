<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Queue {

	protected static $queue = array();

	public static function get($name) {
		if (!isset(Queue::$queue[$name])) {
			Loader::library('3rdparty/Zend/Queue');

			$config = array(
			'name' => $name,
			'driverOptions' => array(
			'host'     => DB_SERVER,
			'username' => DB_USERNAME,
			'password' => DB_PASSWORD,
			'dbname'   => DB_DATABASE,
			'type'     => 'pdo_mysql'
			)
			);

			Queue::$queue[$name] = new Zend_Queue('Concrete5', $config);
		}
		return Queue::$queue[$name];
	}


}