<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ImageFeature extends Feature {

	public function getFeatureDetailObject($mixed) {
		$fd = new ImageFeatureDetail();
		$fd->setFileID($mixed->getFileID());
		return $fd;
	}

}
