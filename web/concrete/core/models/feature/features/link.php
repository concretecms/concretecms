<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_LinkFeature extends Feature {

	public function getFeatureDetailObject($mixed) {
		$fd = new LinkFeatureDetail();
		$fd->setItemLink($mixed->getFeatureDataLink());
		return $fd;
	}

}
