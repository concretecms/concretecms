<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_AssetGroup {
	
	protected $assetPointers = array();

	public function contains(AssetPointer $ap) {
		foreach($this->assetPointers as $assetPointer) {
			if ($assetPointer->getHandle() == $ap->getHandle() && $assetPointer->getType() == $ap->getType()) {
				return true;
			}
		}
		return false;
	}

	public function addGroup(AssetGroup $item) {
		$assetPointers = $item->getAssetPointers();
		foreach($assetPointers as $assetPointer) {
			if (!$this->contains($assetPointer)) {
				$this->assetPointers[] = $assetPointer;
			}
		}
	}

	public function add(AssetPointer $ap) {
		if (!$this->contains($ap)) {
			$this->assetPointers[] = $ap;
		}
	}

	public function getAssetPointers() {
		return $this->assetPointers;
	}
	
}