<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FileFeature extends Feature {

	public function getFeatureDetailObject($mixed) {
		$fd = new FileFeatureDetail();
		$fd->setFileID($mixed->getFileID());
		return $fd;
	}

}
