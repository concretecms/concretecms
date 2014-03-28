<?
namespace Concrete\Core\Page\Theme;

class ConcretePageTheme extends PageTheme {

	public function registerAssets() {
		$this->providesAsset('css', 'core/frontend/*');
	}

}
