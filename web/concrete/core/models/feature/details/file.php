<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FileFeatureDetail extends FeatureDetail {

	protected $fID;

	public function setFileID($fID) {
		$this->fID = $fID;
	}

	public function getFileID() {
		return $this->fID;
	}

	public function getFileObject() {
		return File::getByID($this->fID);
	}
	
	public static function get($mixed) {
		$fd = new FileFeatureDetail();
		$file = $mixed->getFeatureDataFileObject();
		$fd->setFileID($file->getFileID());
		return $fd;
	}



}
