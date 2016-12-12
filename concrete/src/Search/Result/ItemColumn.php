<?php
namespace Concrete\Core\Search\Result;
class ItemColumn {

	public $key;
	public $value;

	public function getColumnKey() {return $this->key;}
	public function getColumnValue() {return $this->value;}


	public function __construct($key, $value) {
		$this->key = $key;
		$this->value = $value;
	}

}
