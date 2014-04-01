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