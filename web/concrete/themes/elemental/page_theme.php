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
        $this->providesAsset('css', 'blocks/topic_list');
        $this->providesAsset('css', 'core/frontend/*');
	}

    protected $pThemeGridFrameworkHandle = 'bootstrap3';

    public function getThemeBlockClasses()
    {
        return array(
            'feature' => array('feature-home-page'),
            'page_list' => array(
                'recent-blog-entry',
                'page-list-with-buttons',
                'block-sidebar-wrapped'
            ),
            'content' => array('block-sidebar-wrapped'),
        );
    }

    public function getThemeResponsiveImageMap()
    {
        return array(
            'large' => '900px',
            'medium' => '768px',
            'small' => '0'
        );
    }

    public function getThemeEditorClasses()
    {
        return array(
            array('title' => t('Title Thin'), 'menuClass' => 'title-thin', 'spanClass' => 'title-thin'),
            array('title' => t('Title Caps'), 'menuClass' => 'title-caps-bold', 'spanClass' => 'title-caps-bold'),
            array('title' => t('Standard Button'), 'menuClass' => '', 'spanClass' => 'btn btn-default'),
            array('title' => t('Success Button'), 'menuClass' => '', 'spanClass' => 'btn btn-success'),
            array('title' => t('Primary Button'), 'menuClass' => '', 'spanClass' => 'btn btn-primary')
        );
    }

}
