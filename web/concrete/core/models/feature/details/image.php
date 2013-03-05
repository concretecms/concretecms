<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ImageFeatureDetail extends FileFeatureDetail {

	// we have to dupe this because of PHP 5.2's stupidity and our inability to get called class.
	public static function get($mixed) {
		$fd = new ImageFeatureDetail();
		$file = $mixed->getFeatureDataFileObject();
		$fd->setFileID($file->getFileID());
		return $fd;
	}

}
