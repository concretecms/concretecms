<?php
namespace Concrete\Core\Foundation;
class ClassAliasList {

	private static $loc = null;
	public $aliases = array();

	public function getRegisteredAliases() {
		return $this->aliases;
	}

	public static function getInstance() {
		if (null === self::$loc) {
			self::$loc = new self;
		}
		return self::$loc;
	}

	public function register($alias, $class) {
		$this->aliases[$alias] = $class;
	}

	public function registerMultiple($array) {
		foreach($array as $alias => $class) {
			$this->register($alias, $class);
		}
	}

}
