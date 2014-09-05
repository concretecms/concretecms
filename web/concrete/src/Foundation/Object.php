<?php
namespace Concrete\Core\Foundation;
class Object {
	public $error = '';

	/* TODO: move these into an error class */

	function loadError($error) {
		$this->error = $error;
	}

	function isError() {
		$args = func_get_args();
		if (isset($args[0]) && $args[0]) {
			return $this->error == $args[0];
		} else {
			return $this->error;
		}
	}

	function getError() {
		return $this->error;
	}

	public function setPropertiesFromArray($arr) {
		foreach($arr as $key => $prop) {
			$this->{$key} = $prop;
		}
	}

	public static function camelcase($file) {
		return camelcase($file);
	}

	public static function uncamelcase($string) {
		return uncamelcase($string);
	}

}
