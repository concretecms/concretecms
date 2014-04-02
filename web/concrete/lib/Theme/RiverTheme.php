<?
namespace Concrete\Theme;
use \Concrete\Core\Page\Theme\PageTheme;
class RiverTheme extends PageTheme {

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
		$r = ResponseAssetGroup::get();
		$r->requireAsset('javascript', 'jquery/form');
		$r->requireAsset('javascript', 'hoverintent');
		$r->requireAsset('javascript', 'backstretch');
		$r->providesAsset('css', 'core/gathering/display');
	}

}