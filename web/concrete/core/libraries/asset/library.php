<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_Asset {
	
	protected $assetVersion = '1.0';
	
	abstract public function getAssetFiles();

	public static function getByHandle($identifier) {
		$class = Object::camelcase($identifier) . 'Asset';
		return new $class();	
	}

}