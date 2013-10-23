<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageTheme_RiverTheme extends PageTheme {

	protected $pThemeGridFrameworkHandle = 'nine_sixty';
	
	public function getThemeGatheringGridItemMargin() {
		return 0;
	}

	public function getThemeGatheringGridItemWidth() {
		return 207;
	}

	public function getThemeGatheringGridItemHeight() {
		return 146;
	}

	public function registerAssets() {
		$v = View::getInstance();
		$v->requireAsset('javascript', 'jquery/form');
		$v->requireAsset('javascript', 'hoverintent');
		$v->requireAsset('javascript', 'backstretch');
		$v->providesAsset('css', 'core/gathering/display');
	}

}