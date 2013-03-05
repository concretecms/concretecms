<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_DateTimeFeatureDetail extends FeatureDetail {

	protected $datetime;
	protected $feHandle = 'date_time';

	public function setDateTime($datetime) {
		$this->datetime = $datetime;
	}

	public function getDateTime() {
		return $this->datetime;
	}


}
