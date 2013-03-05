<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ImageFeatureDetail extends FileFeatureDetail {

	protected $feHandle = 'image';

	public static function get($mixed) {
		$fd = new ImageFeatureDetail();
		$fd->setFileID($mixed->getFileID());
		return $fd;
	}


}
