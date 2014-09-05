<?php
namespace Concrete\Core\Backup;
use Loader;
class BackupTable {

   //Public Class members
	public $str_createTableSql= "";
	public $str_insertionSql= "";
	//Private Class Members
	private $db = "";
	private $str_tablename= "";
	private $arr_fields = Array();
	private $arr_fieldtypes = Array();
	private $rs_table;
	/**
	* @desc class constructor, should load up the table information and create table sql and
	* insert statements
	* @param string $table case sensitive table name of the table to be processed
	**/
	public function __construct($table)	{
		$this->db = Loader::db();
		$this->db->setFetchMode(ADODB_FETCH_BOTH);
		if (trim($table) == "") {
			return false;
		}
		$this->str_tablename = $table;
		$this->rs_table = $this->db->Execute("SELECT * FROM ".$this->str_tablename);
		if (!$this->rs_table) {
			print "Error while retrieving data from". $this->str_tablename;
		}
		else {
			$this->rs_table->MoveFirst();
			//			while (!$rs_table->EOF){
				 for ($f_it=0; $f_it<$this->rs_table->FieldCount();$f_it++) {
				  $obj_fld = $this->rs_table->FetchField($f_it);
				  $str_fieldtype = $this->rs_table->MetaType($obj_fld->type);
//				 }
			}
			$this->generateCreateTableSql();
			$this->generateDataInsertionSql();
			$this->rs_table->Close();
		}
	}

	/**
	* @desc determines weather or not a adodb field type is a quoted type
	* @param string $type the adodb field type specifier (C,X,B,D,T,L,I,N,R) see manual
	* @return boolean
	**/
	public function isQuotedType($type)	{
		switch ($type) {
			case "C":
			case "X":
			case "B":
         case "D":
         case "T":
				return true;
				break;
			default:
				return false;
		}
	}
		/**
		 * generateCreateTableSql
		 * @desc Queries table for creation SQL and sets class variable
		 * @access public
		 * @return void
		 */
		public function generateCreateTableSql() {
		$rs_createsql = $this->db->Execute("SHOW CREATE TABLE ". $this->str_tablename);
		if (!$rs_createsql){
			print "Error while retrieving table creation SQL";
		}
		else {
			$this->str_createTableSql = preg_replace('/CREATE TABLE/','CREATE TABLE IF NOT EXISTS',$rs_createsql->fields['Create Table']). ";";
			$rs_createsql->close();
		}
	}

	public function generateDataInsertionSql() {
		$this->rs_table->MoveFirst(); // Just in case
		while (!$this->rs_table->EOF) {
			$arr_rowData = Array();
			for($int_cflds = 0;$int_cflds < $this->rs_table->FieldCount();$int_cflds++) {
				$obj_fld = $this->rs_table->FetchField($int_cflds);
				$str_fieldtype = $this->rs_table->MetaType($obj_fld->type);
				if ($this->isQuotedType($str_fieldtype)) {
					$str_fieldData = $this->db->qstr($this->rs_table->fields[$obj_fld->name]);
					if ($str_fieldData == "") {
					   $str_fieldData = "''";
					}
				}
				else {
					$str_fieldData = $this->rs_table->fields[$obj_fld->name];
					if ($str_fieldData == "") {
					   $str_fieldData = "NULL";
					}
				}
	            $arr_rowData[] = $str_fieldData;
			}
			if ($this->str_insertionSql == "") {
				$this->str_insertionSql = "INSERT INTO ". $this->str_tablename ." VALUES(".implode(",",$arr_rowData).")";
			}
			else {
				$this->str_insertionSql .= "\n ,(".implode(",",$arr_rowData).")";
			}
			$this->rs_table->MoveNext();
			}
         if ($this->str_insertionSql != "") {
            $this->str_insertionSql .= ";";
         }
	}
}
