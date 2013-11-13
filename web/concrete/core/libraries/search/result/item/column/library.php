<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_SearchResultItemColumn {

	public $key;
	public $value;

	public function getColumnKey() {return $this->key;}
	public function getColumnValue() {return $this->value;}


	public function __construct($key, $value) {
		$this->key = $key;
		$this->value = $value;
	}

}
