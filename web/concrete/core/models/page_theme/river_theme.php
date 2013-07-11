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

	public function __construct() {
		$req = Request::get();
		$req->requireAsset('javascript', 'jquery/form');
		$req->requireAsset('javascript', 'hoverintent');
		$req->requireAsset('javascript', 'backstretch');
		$this->providesAsset('css', 'core/gathering');
	}

}