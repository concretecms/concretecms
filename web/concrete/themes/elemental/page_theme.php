<?
namespace Concrete\Theme\Elemental;
class PageTheme extends \Concrete\Core\Page\Theme\Theme {

	public function registerAssets() {
        //$this->providesAsset('javascript', 'bootstrap/*');
        $this->providesAsset('css', 'bootstrap/*');
        $this->providesAsset('css', 'blocks/form');
        $this->providesAsset('css', 'blocks/social_links');
        $this->providesAsset('css', 'core/frontend/*');
	}

    protected $pThemeGridFrameworkHandle = 'bootstrap3';

}
