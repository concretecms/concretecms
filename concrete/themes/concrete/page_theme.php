<?php
namespace Concrete\Theme\Concrete;

class PageTheme extends \Concrete\Core\Page\Theme\Theme
{
    public function registerAssets()
    {
        $this->providesAsset('javascript', 'jquery');
        $this->providesAsset('javascript', 'bootstrap');
        $this->providesAsset('nprogress');

        $this->requireAsset('javascript', 'backstretch');

    }
}
