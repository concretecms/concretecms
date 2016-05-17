<?php
namespace Concrete\Theme\Concrete;

class PageTheme extends \Concrete\Core\Page\Theme\Theme
{
    public function registerAssets()
    {
        $this->providesAsset('css', 'core/frontend/*');
        $this->requireAsset('javascript-conditional', 'html5-shiv');
        $this->requireAsset('javascript-conditional', 'respond');
    }
}
