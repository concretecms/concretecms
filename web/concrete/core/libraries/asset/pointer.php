<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_AssetPointer {
	
	protected $assetType;
	protected $assetHandle;
	
	public function getType() {return $this->assetType;}
	public function getHandle() {return $this->assetHandle;}
	
	public function __construct($assetType, $assetHandle) {
		$this->assetType = $assetType;
		$this->assetHandle = $assetHandle;
	}

	public function getAsset() {
		$al = AssetList::getInstance();
		return $al->getAsset($this->assetType, $this->assetHandle);
	}
	
}