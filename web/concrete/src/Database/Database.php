<?php

namespace Concrete\Core\Database;
use Doctrine\DBAL\Configuration as DoctrineConfiguration;

class Database {

	protected static $activeConnection = false;
	protected static $connections = array();

    /**
     * @return \Doctrine\DBAL\Connection
     */
	public static function getActiveConnection() {
		if (!static::$activeConnection) {
			static::$connections['default'] = static::createDefaultConnection();
			static::$activeConnection = 'default';
		}
		return static::$connections[static::$activeConnection];
	}

	public function setActiveConnection($name) {
		static::$activeConnection = $name;
	}

	protected static function createDefaultConnection() {
        $config = \Core::make('config');

        $connections = $config->get('database.connections');
        $default = $connections[$config->get('database.default-connection')];

        $drivers = $config->get('database.drivers');

        if (!$default) {
            $connections = $config->get('site_install.database.connections');
            $default = $connections[$config->get('site_install.database.default-connection')];
        }
        if (isset($drivers[$default['driver']])) {
            return static::connect(array(
                                       'host' => $default['server'],
                                       'user' => $default['username'],
                                       'password' => $default['password'],
                                       'database' => $default['database'],
                                       'charset' => $default['charset'],
                                       'driverClass' => $drivers[$default['driver']],
                                   ));
        }

        return static::connect(array(
                                   'host' => $default['server'],
                                   'user' => $default['username'],
                                   'password' => $default['password'],
                                   'database' => $default['database'],
                                   'charset' => $default['charset'],
                                   'driver' => $default['driver']
                               ));
	}

	public static function connect($configuration) {
		$defaults = array(
			'host' => 'localhost',
			'charset' => 'utf8'
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
