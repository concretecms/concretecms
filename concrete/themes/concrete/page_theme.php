<?php
namespace Concrete\Theme\Concrete;

class PageTheme extends \Concrete\Core\Page\Theme\Theme
{
    public function registerAssets()
    {
        $this->requireAsset('font-awesome');
        $this->requireAsset('jquery');
    }
}
