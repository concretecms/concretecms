<?php
namespace Concrete\Core\Database;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Class Connection
 * @package Concrete\Core\Database
 */
class Connection extends \Doctrine\DBAL\Connection
{

	/** @var EntityManager $entityManager */
	protected $_entityManager = null;

	/**
	 * Returns the entity manager for use with Doctrine ORM
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		if ($this->_entityManager === null) {
			$devMode = true;
			$config = Setup::createXMLMetadataConfiguration(array(DIR_ORM_ENTITIES), $devMode);
			$conn = $this->getParams();
			$this->_entityManager = EntityManager::create($conn, $config);
		}
		return $this->_entityManager;
	}

	/**
	 * Used to determine if a table already exists in the database. This function is not case sensitive.
	 * @param string $tableName
	 * @return boolean Returns true if the table exists, false if it does not.
	 */
	public function tableExists($tableName)
	{
		$sm = $this->getSchemaManager();
		$schemaTables = $sm->listTables();
		$tables = array();
		foreach ($schemaTables as $table) {
			$tables[] = strtolower((string)$table->getName());
		}
		return in_array(strtolower($tableName), $tables);
	}

	/**
	 * @return \Doctrine\DBAL\Driver\Statement|mixed
	 */
	public function query()
	{
		$args = func_get_args();
		if (isset($args) && isset($args[1]) && (is_string($args[1]) || is_array($args[1]))) {
			return $this->executeQuery($args[0], $args[1]);
		} else {
			return call_user_func_array('parent::query', $args);
		}
	}

	/**
	 * Below this point are just deprecated functions, mostly for ADODB backwards comparability
	 */

	/**
	 * @deprecated
	 * @param $q
	 * @param array $arguments
	 * @return bool|\Doctrine\DBAL\Driver\Statement
	 */
	public function Execute($q, $arguments = array())
	{
		if ($q instanceof \Doctrine\DBAL\Statement) {
			return $q->execute($arguments);
		} else {
			if (!is_array($arguments)) {
				$arguments = array($arguments); // adodb backward compatibility
			}
			return $this->executeQuery($q, $arguments);
		}
	}

	/**
	 * Alias to old ADODB method
	 * @deprecated
	 * @param $q
	 * @param array $arguments
	 * @return array
	 */
	public function GetRow($q, $arguments = array())
	{
		if (!is_array($arguments)) {
			$arguments = array($arguments); // adodb backward compatibility
		}
		$r = $this->fetchAssoc($q, $arguments);
		if (!is_array($r)) {
			$r = array();
		}
		return $r;
	}

	/**
	 * alias to old ADODB method
	 * @deprecated
	 * @param $string
	 * @return string
	 */
	public function qstr($string)
	{
		return $this->quote($string);
	}

	/**
	 * alias to old ADODB method
	 * @deprecated
	 * @param $q
	 * @param array $arguments
	 * @return mixed
	 */
	public function GetOne($q, $arguments = array())
	{
		if (!is_array($arguments)) {
			$arguments = array($arguments); // adodb backward compatibility
		}
		return $this->fetchColumn($q, $arguments, 0);
	}

	/**
	 * alias to old ADODB method
	 * @deprecated
	 * @return bool|int If the error code is greater than zero then return the error code number, otherwise return false
	 */
	public function ErrorMsg()
	{
		if ($this->errorCode() > 0) {
			return $this->errorCode();
		}

		return false;
	}

	/**
	 * alias to old ADODB method
	 * @deprecated
	 * @param string $q query statement
	 * @param array|string $arguments
	 * @return array
	 */
	public function GetAll($q, $arguments = array())
	{
		if (!is_array($arguments)) {
			$arguments = array($arguments); // adodb backward compatibility
		}
		return $this->fetchAll($q, $arguments);
	}

	/**
	 * alias to old ADODB method
	 * @deprecated
	 * @param string $q query statement
	 * @param array|string $arguments
	 * @return array
	 */
	public function GetArray($q, $arguments = array())
	{
		return $this->GetAll($q, $arguments);
	}

	/**
	 * Returns an associative array of all columns in a table
	 * @deprecated
	 * @param string $table the name of the table
	 * @return array
	 */
	public function MetaColumnNames($table)
	{
		$sm = $this->getSchemaManager();
		$columnNames = array();
		$columns = $sm->listTableColumns($table);
		foreach ($columns as $column) {
			$columnNames[] = $column->getName();
		}
		return $columnNames;
	}

	/**
	 * Alias to old ADODB Replace() method.
	 * @deprecated
	 * @param string $table the name of the table
	 * @param string|array $fieldArray
	 * @param string|array $keyCol
	 * @param bool $autoQuote
	 */
	public function Replace($table, $fieldArray, $keyCol, $autoQuote = true)
	{
		$qb = $this->createQueryBuilder();
		$qb->select('count(*)')->from($table, 't');
		$where = $qb->expr()->andX();
		$updateKeys = array();
		if (!is_array($keyCol)) {
			$keyCol = array($keyCol);
		}
		foreach ($keyCol as $key) {
			$field = $fieldArray[$key];
			$updateKeys[$key] = $field;
			if ($autoQuote) {
				$field = $qb->expr()->literal($field);
			}
			$where->add($qb->expr()->eq($key, $field));
		}
		$qb->where($where);
		$num = $this->query($qb->getSql())->fetchColumn();
		if ($num < 1) {
			$this->insert($table, $fieldArray);
		} else {
			$this->update($table, $fieldArray, $updateKeys);
		}
	}

	/**
	 * alias to old ADODB method
	 * @deprecated
	 * @param string $q query statement
	 * @param array $arguments
	 * @return array
	 */
	public function GetCol($q, $arguments = array())
	{
		$r = $this->fetchAll($q, $arguments);
		$return = array();

		foreach ($r as $value) {
			$return[] = $value[key($value)];
		}
		return $return;
	}


	/**
	 * @deprecated
	 * alias to old ADODB method
	 */
	public function Insert_ID()
	{
		return $this->lastInsertId();
	}

	/**
	 * @deprecated
	 * @return array
	 */
	public function MetaTables()
	{
		$sm = $this->getSchemaManager();
		$schemaTables = $sm->listTables();
		$tables = array();
		foreach ($schemaTables as $table) {
			$tables[] = $table->getName();
		}
		return $tables;
	}

	/**
	 * @deprecated
	 * @param $table
	 * @return \Doctrine\DBAL\Schema\Column[]
	 */
	public function MetaColumns($table)
	{
		$sm = $this->getSchemaManager();
		$schemaColumns = $sm->listTableColumns($table);
		return $schemaColumns;

	}
}