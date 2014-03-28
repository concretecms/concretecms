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
 * @see Zend_Db_Statement
 */
require_once "Zend/Db/Statement.php";

/**
 * @package Whitewashing
 * @subpackage Db
 */
class Zend_Db_Statement_Mysql extends Zend_Db_Statement
{
    /**
     * @var resource
     */
    protected $_stmt;

    /**
     * @var string
     */
    protected $_preparedSql;

    /**
     * Prepares statement handle
     *
     * @param string $sql
     * @return void
     * @throws Zend_Db_Statement_Oracle_Exception
     */
    protected function _prepare($sql)
    {
        $this->_preparedSql = $sql;
    }

    /**
     * @param array $params
     */
    protected function _execute(array $params = null)
    {
        $sql = str_replace('?', '%s', $this->_preparedSql);

        // if no params were given as an argument to execute(),
        // then default to the _bindParam array
        if ($params === null) {
            $params = $this->_bindParam;
        }
        // send $params as input parameters to the statement
        if ($params) {
            $newParams = array();
            foreach($params AS $k => $v) {
                $newParams[$k] = $this->_adapter->quote($v);
            }

            array_unshift($newParams, $sql);
            $sql = call_user_func_array('sprintf', $newParams);
            unset($newParams);
        }
        
        $this->_stmt = mysql_query($sql);
        if($this->_stmt === false) {
            $this->_throwException();
        }
        return true;
    }

    /**
     * Binds a parameter to the specified variable name.
     *
     * @param mixed $parameter Name the parameter, either integer or string.
     * @param mixed $variable  Reference to PHP variable containing the value.
     * @param mixed $type      OPTIONAL Datatype of SQL parameter.
     * @param mixed $length    OPTIONAL Length of SQL parameter.
     * @param mixed $options   OPTIONAL Other options.
     * @return bool
     * @throws Zend_Db_Statement_Mysqli_Exception
     */
    protected function _bindParam($parameter, &$variable, $type = null, $length = null, $options = null)
    {
        return true;
    }

    /**
     * Retrieves the error code, if any, associated with the last operation on
     * the statement handle.
     *
     * @return string error code.
     */
    public function errorCode()
    {
        return mysql_errno($this->_adapter->getConnection());
    }

    /**
     * Retrieves an array of error information, if any, associated with the
     * last operation on the statement handle.
     *
     * @return array
     */
    public function errorInfo()
    {
        return array(
            $this->errorCode(),
            $this->errorMessage()
        );
    }

    private function errorMessage()
    {
        return mysql_error($this->_adapter->getConnection());
    }

    private function _throwException($message=null)
    {
        require_once 'Zend/Db/Statement/Exception.php';
        if($message === null) {
            $message = $this->errorMessage()." (".$this->errorCode().")";
        }
        throw new Zend_Db_Statement_Exception($message);
    }

    /**
     * Retrieves the next rowset (result set) for a SQL statement that has
     * multiple result sets.  An example is a stored procedure that returns
     * the results of multiple queries.
     *
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function nextRowset()
    {
        $this->_throwException("nextRowset() is not implemented");
    }

    /**
     * Returns the number of rows affected by the execution of the
     * last INSERT, DELETE, or UPDATE statement executed by this
     * statement object.
     *
     * @return int     The number of rows affected.
     * @throws Zend_Db_Statement_Exception
     */
    public function rowCount()
    {
        if($this->_stmt == true) {
            return mysql_affected_rows($this->_adapter->getConnection());
        } else if($this->_stmt == null) {
            return -1;
        } else {
            return mysql_num_rows($this->_stmt);
        }
    }

    /**
     * Fetches a row from the result set.
     *
     * @param int $style  OPTIONAL Fetch mode for this fetch operation.
     * @param int $cursor OPTIONAL Absolute, relative, or other.
     * @param int $offset OPTIONAL Number for absolute or relative cursors.
     * @return mixed Array, object, or scalar depending on fetch mode.
     * @throws Zend_Db_Statement_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        // make sure we have a fetch mode
        if ($style === null) {
            $style = $this->_fetchMode;
        }

        $row = false;
        switch ($style) {
            case Zend_Db::FETCH_NUM:
                $row = mysql_fetch_row($this->_stmt);
                break;
            case Zend_Db::FETCH_ASSOC:
                $row = mysql_fetch_assoc($this->_stmt);
                break;
            case Zend_Db::FETCH_BOTH:
                $row = mysql_fetch_array($this->_stmt);
                break;
            case Zend_Db::FETCH_OBJ:
                $row = mysql_fetch_object($this->_stmt);
                break;
            case Zend_Db::FETCH_BOUND:
                $row = $this->_fetchBound(mysql_fetch_array($this->_stmt));
                break;
            default:
                $this->_throwException("Fetch Style '".$style."' not supported!");
                break;
        }
        return $row;
    }

    /**
     * Closes the cursor, allowing the statement to be executed again.
     *
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function closeCursor()
    {
        if(is_resource($this->_stmt)) {
            mysql_free_result($this->_stmt);
        }
        $this->_stmt = null;
        return true;
    }

    /**
     * Returns the number of columns in the result set.
     * Returns null if the statement has no result set metadata.
     *
     * @return int The number of columns.
     * @throws Zend_Db_Statement_Exception
     */
    public function columnCount()
    {
        if(is_resource($this->_stmt)) {
            return mysql_num_fields($this->_stmt);
        } else {
            return 0;
        }
    }
}