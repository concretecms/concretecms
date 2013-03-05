<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FileFeatureDetail extends FileFeatureDetail {

	protected $feHandle = 'file';
	protected $fID;
	protected $fObject;

	public function setFileID($fID) {
		$this->fID = $fID;
		$this->fObject = File::getByID($fID);
	}

	public function getFileObject() {
		return $this->fObject;
	}

	public function getFileID() {
		return $this->fID;
	}

}
