<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_BodyFeatureDetail extends FeatureDetail {

	protected $feHandle = 'body';
	protected $content;

	public function setBodyContent($content) {
		$this->content = $content;
	}

	public function getBodyContent() {
		return $this->content;
	}

	public static function get($mixed) {
		$fd = new BodyFeatureDetail();
		$fd->setBodyContent($mixed->getFeatureDataBodyContent());
		return $fd;
	}

}
