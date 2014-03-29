<?
namespace Concrete\Theme;
use Concrete\Core\Page\Theme\PageTheme;
class ConcretePageTheme extends PageTheme {

	public function registerAssets() {
		$this->providesAsset('css', 'core/frontend/*');
	}

}
