<?php

namespace Concrete\Core\StyleCustomizer\WebFont;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;

class WebFontCollectionFactory
{

    /**
     * @param PresetInterface $preset
     * @return WebFontCollection
     */
    public function createFromPreset(PresetInterface $preset): WebFontCollection
    {
        $presetIdentifier = $preset->getIdentifier();
        $collection = new WebFontCollection();
        $file = $preset->getTheme()->getThemeCustomizer()->getConfigurationFile();
        if ($file) {
            $xml = simplexml_load_file($file);
            if ($xml->webfonts) {
                foreach ($xml->webfonts->preset as $presetNode) {
                    if ((string) $presetNode['identifier'] == $presetIdentifier) {
                        foreach ($presetNode->font as $fontNode) {
                            $font = new WebFont((string) $fontNode['name'], (string) $fontNode['type']);
                            $collection->add($font);
                        }
                    }
                }
            }
        }
        return $collection;
    }


}
