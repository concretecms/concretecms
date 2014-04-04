<?
namespace Concrete\Theme\TwitterBootstrap;
class PageTheme extends \Concrete\Core\Page\Theme\PageTheme {

	protected $pThemeGridFrameworkHandle = 'bootstrap2';

	public function getThemeGatheringGridItemMargin() {
		return 20;
	}

	public function getThemeGatheringGridItemWidth() {
		return 146;
	}

	public function getThemeGatheringGridItemHeight() {
		return 146;
	}

	public function registerAssets() {
		$this->providesAsset('javascript', 'bootstrap/*');
		$this->providesAsset('css', 'bootstrap/*');
		$this->providesAsset('css', 'blocks/form');
		$this->providesAsset('css', 'core/frontend/*');
	}

}
