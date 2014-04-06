<?php

namespace Concrete\Core\Database;

class Connection extends \Doctrine\DBAL\Connection {

	/** 
	 * @deprecated
	 */
	public function Execute($q, $arguments = array()) {
		if ($q instanceof \Doctrine\DBAL\Statement) {
			return $q->execute($arguments);
		} else {
			return $this->executeQuery($q, $arguments);
		}
	}

    public function query() {
        $args = func_get_args();
        if (isset($args) && isset($args[1]) && is_array($args[1])) {
            return $this->executeQuery($args[0], $args[1]);
        } else {
            return call_user_func_array('parent::query', $args);
        }
    }
    /** 
     * @deprecated
     * alias to old ADODB method
     */
    public function GetRow($q, $arguments = array()) {
        $r = $this->fetchAssoc($q, $arguments);
        if (!is_array($r)) {
            $r = array();
        }
        return $r;
    }

    /** 
     * @deprecated
     * alias to old ADODB method
     */
    public function GetOne($q, $arguments = array()) {
        return $this->fetchColumn($q, $arguments, 0);
    }

    /** 
     * @deprecated
     * alias to old ADODB method
     */
    public function GetAll($q, $arguments = array()) {
        return $this->fetchAll($q, $arguments);
    }


    /** 
     * @deprecated
     * Returns an associative array of all columns in a table
     */
    public function MetaColumnNames($table) {
        $sm = $this->getSchemaManager();
        $columnNames = array();
        $columns = $sm->listTableColumns($table);
        foreach($columns as $column) {
            $columnNames[] = $column->getName();
        }
        return $columnNames;
    }

    /** 
     * @deprecated
     * Alias to old ADODB Replace() method.
     */
    public function Replace($table, $fieldArray, $keyCol, $autoQuote = true) {
        $qb = $this->createQueryBuilder();
        $qb->select('count(*)')->from($table, 't');
        $where = $qb->expr()->andX();
        $updateKeys = array();
        if (!is_array($keyCol)) {
            $keyCol = array($keyCol);
        }
        foreach($keyCol as $key) {
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
     * @deprecated
     * alias to old ADODB method
     */
    public function GetCol($q, $arguments = array()) {
        return $this->fetchAll($q, $arguments);
    }


    /** 
     * @deprecated
     * alias to old ADODB method
     */
    public function Insert_ID() {
    	return $this->lastInsertId();
    }

    /** 
     * @deprecated
     */
    public function MetaTables() {
        $sm = $this->getSchemaManager();
        $schemaTables = $sm->listTables();
        $tables = array();
        foreach($schemaTables as $table) {
            $tables[] = $table->getName();
        }
        return $tables;

    }

}