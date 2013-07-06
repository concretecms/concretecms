<?

defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_AssetFile {
	
	const ASSET_FILE_POSITION_HEADER = 'H';
	const ASSET_FILE_POSITION_FOOTER = 'F';

	abstract public function getAssetFileDefaultPosition();
	abstract public function getAssetFileDefaultMinify();
	abstract public function getAssetFileDefaultCombine();


}