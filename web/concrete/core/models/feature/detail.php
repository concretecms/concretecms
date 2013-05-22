<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FeatureDetail extends Object {

	protected $item;

	public function __construct($mixed) {
		$this->item = $mixed;
	}

	public function getValue() {
		return $this->item;
	}

	public function __toString() {
		$r = $this->getValue();
		if ($r) {
			return $r;
		} else {
			return '';
		}
	}
	
	public function getAggregatorItemSuggestedSlotWidth() {
		return 0;
	}

	public function getAggregatorItemSuggestedSlotHeight() {
		return 0;
	}

	public function handleFeatureAssignment(FeatureAssignment $fa) {
		return false;
	}
	
}
