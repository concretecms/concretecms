<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @author     Benjamin Eberlei (kontakt@beberlei.de)
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 * @package    Whitewashing
 * @subpackage Db
 */

/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once "Zend/Db/Adapter/Abstract.php";

/**
 * @see Whitewashing_Db_Statement_Mysql
 */
require_once "Zend/Db/Statement/Mysql.php";

class Zend_Db_Adapter_Mysql extends Zend_Db_Adapter_Abstract
{
    /**
     * Keys are UPPERCASE SQL datatypes or the constants
     * Zend_Db::INT_TYPE, Zend_Db::BIGINT_TYPE, or Zend_Db::FLOAT_TYPE.
     *
     * Values are:
     * 0 = 32-bit integer
     * 1 = 64-bit integer
     * 2 = float or decimal
     *
     * @var array Associative array of datatypes to values 0, 1, or 2.
     */
    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'INT'                => Zend_Db::INT_TYPE,
        'INTEGER'            => Zend_Db::INT_TYPE,
        'MEDIUMINT'          => Zend_Db::INT_TYPE,
        'SMALLINT'           => Zend_Db::INT_TYPE,
        'TINYINT'            => Zend_Db::INT_TYPE,
        'BIGINT'             => Zend_Db::BIGINT_TYPE,
        'SERIAL'             => Zend_Db::BIGINT_TYPE,
        'DEC'                => Zend_Db::FLOAT_TYPE,
        'DECIMAL'            => Zend_Db::FLOAT_TYPE,
        'DOUBLE'             => Zend_Db::FLOAT_TYPE,
        'DOUBLE PRECISION'   => Zend_Db::FLOAT_TYPE,
        'FIXED'              => Zend_Db::FLOAT_TYPE,
        'FLOAT'              => Zend_Db::FLOAT_TYPE
    );

    public function setConnectionResource($connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $tables = array();
        $query = "SHOW TABLES FROM ".$this->quoteIdentifier($this->_config['dbname']);
        if($rs = mysql_query($query)) {
            while($row = mysql_fetch_row($rs)) {
                $tables[] = $row[0];
            }
        }
        return $tables;
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME => string; name of database or schema
     * TABLE_NAME  => string;
     * COLUMN_NAME => string; column name
     * COLUMN_POSITION => number; ordinal position of column in table
     * DATA_TYPE   => string; SQL datatype name of column
     * DEFAULT     => string; default expression of column, null if none
     * NULLABLE    => boolean; true if column can have nulls
     * LENGTH      => number; length of CHAR/VARCHAR
     * SCALE       => number; scale of NUMERIC/DECIMAL
     * PRECISION   => number; precision of NUMERIC/DECIMAL
     * UNSIGNED    => boolean; unsigned property of an integer type
     * PRIMARY     => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        /**
         * @todo  use INFORMATION_SCHEMA someday when
         * MySQL's implementation isn't too slow.
         */

        if ($schemaName) {
            $sql = 'DESCRIBE ' . $this->quoteIdentifier("$schemaName.$tableName", true);
        } else {
            $sql = 'DESCRIBE ' . $this->quoteIdentifier($tableName, true);
        }

        /**
         * Use mysqli extension API, because DESCRIBE doesn't work
         * well as a prepared statement on MySQL 4.1.
         */
        if ($queryResult = mysql_query($sql)) {
            while ($row = mysql_fetch_assoc($queryResult)) {
                $result[] = $row;
            }
            mysql_free_result($queryResult);
        } else {
            /**
             * @see Zend_Db_Adapter_Mysqli_Exception
             */
            require_once 'Zend/Db/Adapter/Mysqli/Exception.php';
            throw new Zend_Db_Adapter_Mysqli_Exception($this->getConnection()->error);
        }

        $desc = array();

        $row_defaults = array(
            'Length'          => null,
            'Scale'           => null,
            'Precision'       => null,
            'Unsigned'        => null,
            'Primary'         => false,
            'PrimaryPosition' => null,
            'Identity'        => false
        );
        $i = 1;
        $p = 1;
        foreach ($result as $key => $row) {
            $row = array_merge($row_defaults, $row);
            if (preg_match('/unsigned/', $row['Type'])) {
                $row['Unsigned'] = true;
            }
            if (preg_match('/^((?:var)?char)\((\d+)\)/', $row['Type'], $matches)) {
                $row['Type'] = $matches[1];
                $row['Length'] = $matches[2];
            } else if (preg_match('/^decimal\((\d+),(\d+)\)/', $row['Type'], $matches)) {
                $row['Type'] = 'decimal';
                $row['Precision'] = $matches[1];
                $row['Scale'] = $matches[2];
            } else if (preg_match('/^float\((\d+),(\d+)\)/', $row['Type'], $matches)) {
                $row['Type'] = 'float';
                $row['Precision'] = $matches[1];
                $row['Scale'] = $matches[2];
            } else if (preg_match('/^((?:big|medium|small|tiny)?int)\((\d+)\)/', $row['Type'], $matches)) {
                $row['Type'] = $matches[1];
                /**
                 * The optional argument of a MySQL int type is not precision
                 * or length; it is only a hint for display width.
                 */
            }
            if (strtoupper($row['Key']) == 'PRI') {
                $row['Primary'] = true;
                $row['PrimaryPosition'] = $p;
                if ($row['Extra'] == 'auto_increment') {
                    $row['Identity'] = true;
                } else {
                    $row['Identity'] = false;
                }
                ++$p;
            }
            $desc[$this->foldCase($row['Field'])] = array(
                'SCHEMA_NAME'      => null, // @todo
                'TABLE_NAME'       => $this->foldCase($tableName),
                'COLUMN_NAME'      => $this->foldCase($row['Field']),
                'COLUMN_POSITION'  => $i,
                'DATA_TYPE'        => $row['Type'],
                'DEFAULT'          => $row['Default'],
                'NULLABLE'         => (bool) ($row['Null'] == 'YES'),
                'LENGTH'           => $row['Length'],
                'SCALE'            => $row['Scale'],
                'PRECISION'        => $row['Precision'],
                'UNSIGNED'         => $row['Unsigned'],
                'PRIMARY'          => $row['Primary'],
                'PRIMARY_POSITION' => $row['PrimaryPosition'],
                'IDENTITY'         => $row['Identity']
            );
            ++$i;
        }
        return $desc;
    }

    /**
     * Creates a connection to the database.
     *
     * @return void
     */
    protected function _connect()
    {
        if(is_resource($this->_connection)) {
            return;
        }

        $this->_connection = @mysql_connect(
            $this->_config['host'],
            $this->_config['username'],
            $this->_config['password']
        );
        if($this->_connection == false) {
            $this->_throwException();
        }

        if(@mysql_select_db($this->_config['dbname'], $this->_connection) == false) {
            $this->_throwException();
        }

        if (!empty($this->_config['charset'])) {
            mysql_set_charset($this->_config['charset'], $this->_connection);
        }
    }

    private function _throwException($message=null)
    {
        require_once "Zend/Db/Adapter/Mysql/Exception.php";
        if($message === null) {
            $message = mysql_error($this->_connection)." (".mysql_errno($this->_connection).")";
        }
        throw new Zend_Db_Adapter_Mysql_Exception($message);
    }

    /**
     * Test if a connection is active
     *
     * @return boolean
     */
    public function isConnected()
    {
        return(is_resource($this->_connection));
    }

    /**
     * Force the connection to close.
     *
     * @return void
     */
    public function closeConnection()
    {
        if($this->isConnected()) {
            mysql_close($this->_connection);
            $this->_connection = null;
        }
    }

    /**
     * Prepare a statement and return a PDOStatement-like object.
     *
     * @param string|Zend_Db_Select $sql SQL query
     * @return Zend_Db_Statement|PDOStatement
     */
    public function prepare($sql)
    {
        return new Zend_Db_Statement_Mysql($this, $sql);
    }

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return string
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        return (string)mysql_insert_id($this->_connection);
    }

    /**
     * Begin a transaction.
     */
    protected function _beginTransaction()
    {
        $this->_connect();
        mysql_query("START TRANSACTION");
    }

    /**
     * Commit a transaction.
     */
    protected function _commit()
    {
        mysql_query("COMMIT");
    }

    /**
     * Roll-back a transaction.
     */
    protected function _rollBack()
    {
        mysql_query("ROLLBACK");
    }

    /**
     * Set the fetch mode.
     *
     * @param integer $mode
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    public function setFetchMode($mode)
    {
        switch($mode) {
            case Zend_Db::FETCH_ASSOC:
            case Zend_Db::FETCH_NUM:
            case Zend_Db::FETCH_BOTH:
            case Zend_Db::FETCH_OBJ:
            case Zend_Db::FETCH_BOUND:
                $this->_fetchMode = $mode;
                break;
            case Zend_Db::FETCH_NAMED:
            case Zend_Db::FETCH_LAZY:
            default:
                $this->_throwException("Invalid fetch mode '".$mode."' specified");
        }
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param mixed $sql
     * @param integer $count
     * @param integer $offset
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
        $count = intval($count);
        if ($count <= 0) {
            $this->_throwException("LIMIT argument count=$count is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            $this->_throwException("LIMIT argument offset=$offset is not valid");
        }

        $sql .= " LIMIT $count";
        if ($offset > 0) {
            $sql .= " OFFSET $offset";
        }

        return $sql;
    }

    /**
     * Check if the adapter supports real SQL parameters.
     *
     * @param string $type 'positional' or 'named'
     * @return bool
     */
    public function supportsParameters($type)
    {
        if($type == 'positional') {
            return true;
        }
        return false;
    }

    /**
     * Retrieve server version in PHP style
     *
     * @return string
     */
    public function getServerVersion()
    {
        $this->_connect();
        return mysql_get_server_info($this->_connection);
    }

    /**
     * Returns the symbol the adapter uses for delimited identifiers.
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return '`';
    }

    /**
     * Quote a raw string.
     *
     * @param mixed $value Raw string
     *
     * @return string           Quoted string
     */
    protected function _quote($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        $this->_connect();
        return "'" . mysql_real_escape_string($value, $this->_connection) . "'";
    }
}