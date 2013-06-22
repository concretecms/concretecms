<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageTheme_RiverTheme extends PageTheme {

	protected $ptGridFrameworkHandle = 'nine_sixty';
	
	public function getThemeGatheringGridItemMargin() {
		return 0;
	}

	public function getThemeGatheringGridItemWidth() {
		return 207;
	}

	public function getThemeGatheringGridItemHeight() {
		return 146;
	}

}