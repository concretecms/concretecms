<?
namespace Concrete\Theme\Elemental;
class PageTheme extends \Concrete\Core\Page\Theme\Theme {

	public function registerAssets() {
        //$this->providesAsset('javascript', 'bootstrap/*');
        $this->providesAsset('css', 'bootstrap/*');
        $this->providesAsset('css', 'blocks/form');
        $this->providesAsset('css', 'blocks/social_links');
        $this->providesAsset('css', 'blocks/feature');
        $this->providesAsset('css', 'blocks/faq');
        //$this->providesAsset('css', 'blocks/image_slider');
        $this->providesAsset('css', 'core/frontend/*');
	}

    protected $pThemeGridFrameworkHandle = 'bootstrap3';

    public function getThemeBlockClasses()
    {
        return array(
            'feature' => array('ccm-block-feature-home-page')
        );
    }


    public function getThemeEditorClasses()
    {
        return array(

        );
    }

}
