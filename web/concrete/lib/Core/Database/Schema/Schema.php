<?php

namespace Concrete\Core\Database\Schema;

class Schema {

	public static function loadFromXMLFile($file) {
		$sx = simplexml_load_file($file);
		$parser = static::getSchemaParser($sx);
		return $parser->parse();
	}

	protected function getSchemaParser(\SimpleXMLElement $sx) {
		switch($sx['version']) {
			case '0.3':
				$parser = new \Concrete\Core\Database\Schema\Parser\Legacy($sx);
				break;
			default:
				$parser = new \Concrete\Core\Database\Schema\Parser\Concrete($sx);
				break;
		}

		return $parser;
	}
}