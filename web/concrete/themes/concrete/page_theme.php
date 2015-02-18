<?php
namespace Concrete\Theme\Concrete;

class PageTheme extends \Concrete\Core\Page\Theme\Theme
{

    public function registerAssets()
    {
        $this->providesAsset('css', 'core/frontend/*');
    }

}
