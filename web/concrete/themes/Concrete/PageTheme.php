<?
namespace Concrete\Theme\Concrete;
class PageTheme extends \Concrete\Core\Page\Theme\PageTheme {

	public function registerAssets() {
		$this->providesAsset('css', 'core/frontend/*');
	}

}
