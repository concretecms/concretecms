<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_Asset {

	protected $assetVersion = '1.0';
	protected $assetHandle;
	protected $weight = 0;
	protected $local = true;

	const ASSET_POSITION_HEADER = 'H';
	const ASSET_POSITION_FOOTER = 'F';

	abstract public function getAssetDefaultPosition();
	abstract public function getAssetDefaultMinify();
	abstract public function getAssetDefaultCombine();
	abstract public function getAssetPath();

	abstract public function __toString();

	public function __construct($assetHandle) {
		$this->assetHandle = $assetHandle;
		$this->position = $this->getAssetDefaultPosition();
	}

	public function getAssetFilename() {
		return $this->filename;
	}

	public function setAssetFilename($filename) {
		$this->filename = $filename;
	}
	
	public function setAssetWeight($weight) {
		$this->weight = $weight;
	}

	public function setAssetPosition($position) {
		$this->position = $position;
	}
	public function isAssetLocal() {return $this->local;}

	public function setAssetIsLocal($isLocal) {
		$this->local = $isLocal;
	}

	public function getAssetPosition() {
		return $this->position;
	}

	public function getAssetWeight() {return $this->weight;}

}