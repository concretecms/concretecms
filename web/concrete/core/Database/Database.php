<?php
namespace Concrete\Core\Database;

use \Doctrine\DBAL\DriverManager;

/**
 * Class Database
 * @package Concrete\Core\Database
 */
class Database
{
	protected $_activeConnection = null;
	protected $_connections = array();

	public function __construct()
	{
		$this->_connections['default'] = static::createDefaultConnection();
		$this->_activeConnection = 'default';
	}

	/**
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getActiveConnection()
	{
		return $this->_connections[$this->_activeConnection];
	}

	protected static function createDefaultConnection()
	{
		return static::connect(array(
			'host' => DB_SERVER,
			'user' => DB_USERNAME,
			'password' => DB_PASSWORD,
			'database' => DB_DATABASE
		));
	}

	/**
	 * @param array $configuration
	 * @return \Doctrine\DBAL\Connection
	 */
	public static function connect($configuration)
	{
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
			$doctrineConfiguration['driverClass'] = '\Concrete\Core\Database\Driver\PDOMySqlConcrete5\Driver';
		}
		$doctrineConfiguration['wrapperClass'] = '\Concrete\Core\Database\Connection';
		$connection = DriverManager::getConnection($doctrineConfiguration, $config);

		return $connection;
	}


	/**
	 * @deprecated
	 */
	public static function getADOSChema()
	{
		return false;
	}

	/**
	 * @deprecated
	 */
	public function setDebug($_debug)
	{
		return false;
	}

	/**
	 * @deprecated
	 */
	public function getDebug()
	{
		return false;
	}

	/**
	 * @deprecated
	 */
	public function setLogging($log)
	{
		return false;
	}

	/**
	 * @deprecated
	 */
	public static function ensureEncoding()
	{
		return false;
	}

}