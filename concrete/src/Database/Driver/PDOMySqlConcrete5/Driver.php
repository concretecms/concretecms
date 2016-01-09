<?php
namespace Concrete\Core\Database\Driver\PDOMySqlConcrete5;

use Concrete\Core\Database\Connection\PDOConnection;

/**
 * PDO MySql driver.
 *
 * @since 2.0
 */
class Driver extends \Doctrine\DBAL\Driver\PDOMySql\Driver
{

    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        $conn = new PDOConnection(
            $this->_constructPdoDsn($params),
            $username,
            $password,
            $driverOptions
        );

        return $conn;
    }

    /**
     * Constructs the MySql PDO DSN.
     *
     * @param array $params
     *
     * @return string The DSN.
     */
    private function _constructPdoDsn(array $params)
    {
        $dsn = 'mysql:';
        if (isset($params['host']) && $params['host'] != '') {
            $dsn .= 'host=' . $params['host'] . ';';
        }
        if (isset($params['port'])) {
            $dsn .= 'port=' . $params['port'] . ';';
        }
        if (isset($params['database'])) {
            $dsn .= 'dbname=' . $params['database'] . ';';
        }
        if (isset($params['unix_socket'])) {
            $dsn .= 'unix_socket=' . $params['unix_socket'] . ';';
        }
        if (isset($params['charset'])) {
            $dsn .= 'charset=' . $params['charset'] . ';';
        }

        return $dsn;
    }

}
