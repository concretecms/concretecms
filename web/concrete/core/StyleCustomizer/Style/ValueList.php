<?php
namespace Concrete\Core\StyleCustomizer\Style;
use Less_Parser;
class ValueList {

	protected $rules = array();

	public static function loadFromLessFile($file) {
		$l = new Less_Parser();
		$parser = $l->parseFile($file, false, true);
		$vl = new static();
		$vl->rules = $parser->rules;
		return $vl;
	}

	public function getRules() {
		return $this->rules;
	}

}