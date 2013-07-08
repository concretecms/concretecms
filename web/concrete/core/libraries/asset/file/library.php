<?

defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_AssetFile {
	
	protected $file = false;
	protected $weight = 0;

	const ASSET_FILE_POSITION_HEADER = 'H';
	const ASSET_FILE_POSITION_FOOTER = 'F';

	abstract public function getAssetFileDefaultPosition();
	abstract public function getAssetFileDefaultMinify();
	abstract public function getAssetFileDefaultCombine();

	abstract public function __toString();

	public function __construct($file) {
		$this->file = $file;
		$this->position = $this->getAssetFileDefaultPosition();
	}

	public function setAssetFileWeight($weight) {
		$this->weight = $weight;
	}

	public function setAssetFilePosition($position) {
		$this->position = $position;
	}

	public function getAssetFilePosition() {
		return $this->position;
	}

	public function getAssetFileWeight() {return $this->weight;}

}