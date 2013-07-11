<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_Asset {

	protected $assetVersion = '1.0';
	protected $assetHandle;
	protected $weight = 0;
	protected $local = true;
	protected $assetURL;
	protected $assetPath;
	protected $assetSupportsPostProcessing = false;

	const ASSET_POSITION_HEADER = 'H';
	const ASSET_POSITION_FOOTER = 'F';

	abstract public function getAssetDefaultPosition();
	abstract public function getAssetType();
	abstract public function populateAssetURLFromFilename($filename);
	abstract public function populateAssetPathFromFilename($filename);
	abstract public function postprocess($assets);
	abstract public function __toString();

	public function assetSupportsPostProcessing() { return $this->local && $this->assetSupportsPostProcessing;}

	public function setAssetSupportsPostProcessing($postprocess) {$this->assetSupportsPostProcessing = $postprocess;}
	
	public function getAssetURL() {return $this->assetURL;}
	public function getAssetPath() {return $this->assetPath;}

	public function getAssetHandle() {return $this->assetHandle;}

	public function __construct($assetHandle = false) {
		$this->assetHandle = $assetHandle;
		$this->position = $this->getAssetDefaultPosition();
	}

	public function getAssetFilename() {
		return $this->filename;
	}

	public function setAssetWeight($weight) {
		$this->weight = $weight;
	}

	public function setAssetPosition($position) {
		$this->position = $position;
	}

	public function setAssetURL($url) {$this->assetURL = $url;}
	public function setAssetPath($path) {$this->assetPath = $path;}

	public function isAssetLocal() {return $this->local;}

	public function setAssetIsLocal($isLocal) {
		$this->local = $isLocal;
	}

	public function getAssetPosition() {
		return $this->position;
	}

	public function getAssetWeight() {return $this->weight;}

}