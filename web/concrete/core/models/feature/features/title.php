<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_TitleFeature extends Feature {

	public function getFeatureDetailObject($mixed) {
		$fd = new TitleFeatureDetail();
		$fd->setTitle($mixed->getFeatureDataTitle());
		return $fd;
	}

}
