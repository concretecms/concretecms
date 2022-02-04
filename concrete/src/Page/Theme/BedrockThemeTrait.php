<?php

namespace Concrete\Core\Page\Theme;

use Concrete\Core\Feature\Features;
use Concrete\Core\Page\Theme\Color\ColorCollection;
use Concrete\Core\Page\Theme\Color\ColorCollectionFactory;

/**
 * This is a trait you can add to your theme's PageTheme class if it is built with the Concrete bedrock. That means
 * it includes the bedrock SCSS and JS files. If your theme's JS and CSS files include these starter assets, you'll
 * automatically need to require jQuery and Bootstrap4, and you'll automatically support the bootstrap4 grid system.
 */
trait BedrockThemeTrait
{

    public function getThemeSupportedFeatures()
    {
        return [
            Features::BASICS,
            Features::TYPOGRAPHY
        ];
    }

    public function registerAssets()
    {
        $this->requireAsset('font-awesome');
        $this->requireAsset('jquery');
        $this->requireAsset('vue');
        $this->requireAsset('bootstrap');
        $this->requireAsset('moment');
    }

    public function getThemeGridFrameworkHandle(): string
    {
        return 'bootstrap5';
    }

    public function getColorCollection(): ?ColorCollection
    {
        $factory = new ColorCollectionFactory();
        return $factory->createFromArray([
            'primary' => t('Primary'),
            'secondary' => t('Secondary'),
            'light' => t('Light'),
            'dark' => t('Dark'),
        ]);
    }
}
