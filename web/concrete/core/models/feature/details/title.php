<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_TitleFeatureDetail extends FeatureDetail {

	protected $title;
	protected $feHandle = 'title';


	public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}


	public static function get($mixed) {
		$fd = new TitleFeatureDetail();
		$fd->setTitle($mixed->getFeatureDataTitle());
		return $fd;
	}

}
