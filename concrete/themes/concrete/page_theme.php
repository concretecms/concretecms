<?php
namespace Concrete\Theme\Concrete;

use Concrete\Core\Feature\Features;
use Concrete\Core\Page\Theme\BedrockThemeTrait;

class PageTheme extends \Concrete\Core\Page\Theme\Theme
{

    use BedrockThemeTrait;

    public function getThemeSupportedFeatures()
    {
        return [
            Features::ACCOUNT,
            Features::DESKTOP,
        ];
    }

}
