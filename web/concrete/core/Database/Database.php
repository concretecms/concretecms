<?php

namespace Concrete\Core\Database;
use Doctrine\DBAL\Configuration as DoctrineConfiguration;

class Database {

	protected static $activeConnection;
	protected static $connections = array();

	public static function getActiveConnection() {
		if (!isset(static::$activeConnection)) {
			static::$connections['default'] = static::createDefaultConnection();
			static::$activeConnection = 'default';
		}
		return static::$connections[static::$activeConnection];
	}

	protected static function createDefaultConnection() {
		return static::connect(array(
			'host' => DB_SERVER,
			'user' => DB_USERNAME,
			'password' => DB_PASSWORD,
			'database' => DB_DATABASE
		));
	}

	public static function connect($configuration) {
		$defaults = array(
			'host' => 'localhost',
			'charset' => DB_CHARSET
		);
		// overwrite all the defaults with the arguments
		$configuration = array_merge($defaults, $configuration);

		$config = new Configuration();

		// now we take our sensible defaults and we map them to the 
		// doctrine configuration array.
		$doctrineConfiguration = $configuration;
		$doctrineConfiguration['dbname'] = $configuration['database'];
		if (!isset($doctrineConfiguration['driver'])) {
			$doctrineConfiguration['driverClass'] = '\Concrete\Core\Database\Driver\PDOMysqlConcrete5\Driver';
		}
		$doctrineConfiguration['wrapperClass'] = '\Concrete\Core\Database\Connection';
		$connection = \Doctrine\DBAL\DriverManager::getConnection($doctrineConfiguration, $config);

		return $connection;
	}


	/** 
	 * @deprecated
	 */
	public static function getADOSChema() {
		return false;
	}

	/** 
	 * @deprecated
	 */
	public function setDebug($_debug) {
		return false;
	}

	/** 
	 * @deprecated
	 */
	public function getDebug() {
		return false;
	}

	/** 
	 * @deprecated
	 */
	public function setLogging($log) {
		return false;
	}

	/** 
	 * @deprecated
	 */
	public static function ensureEncoding() {
		return false;
	}

}