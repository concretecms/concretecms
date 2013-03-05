<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_DateTimeFeature extends Feature {

	public function getFeatureDetailObject($mixed) {
		$fd = new DateTimeFeatureDetail();
		$fd->setDateTime($mixed->getFeatureDataDateTime());
		return $fd;
	}



}
