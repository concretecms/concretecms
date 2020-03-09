<?php
namespace Concrete\Core\Page\Theme;

trait BedrockThemeTrait
{

    public function registerAssets()
    {
        $this->requireAsset('font-awesome');
        $this->requireAsset('jquery');
    }

    public function getThemeGridFrameworkHandle()
    {
        return 'bootstrap4';
    }

}
