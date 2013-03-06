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
		return $this->getValue();
	}
		
}
