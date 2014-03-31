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

    /** 
     * @deprecated
     * alias to old ADODB method
     */
    public function GetRow($q, $arguments = array()) {
        return $this->fetchAssoc($q, $arguments);
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