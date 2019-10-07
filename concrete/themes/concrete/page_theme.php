<?php
namespace Concrete\Theme\Concrete;

class PageTheme extends \Concrete\Core\Page\Theme\Theme
{
    public function registerAssets()
    {
        $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('javascript', 'bootstrap');
        $this->requireAsset('javascript', 'backstretch');
        $this->providesAsset('css', 'core/frontend/*');
    }
}
