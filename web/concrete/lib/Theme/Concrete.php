<?
namespace Concrete\Theme;
use \Concrete\Core\Page\Theme\PageTheme;
class Concrete extends PageTheme {

	public function registerAssets() {
		$this->providesAsset('css', 'core/frontend/*');
	}

}
