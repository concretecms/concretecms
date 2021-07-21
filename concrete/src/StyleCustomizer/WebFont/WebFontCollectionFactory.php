<?php

namespace Concrete\Core\StyleCustomizer\WebFont;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\StyleCustomizer\Skin\PresetSkin;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class WebFontCollectionFactory
{

    /**
     * @param SkinInterface $skin
     * @return WebFontCollection
     */
    public function createFromSkin(SkinInterface $skin): WebFontCollection
    {
        if ($skin instanceof CustomSkin) {
            $skinIdentifier = $skin->getPresetSkinStartingPoint();
        } else {
            $skinIdentifier = $skin->getIdentifier();
        }
        $collection = new WebFontCollection();
        $record = $skin->getTheme()->getStyleConfigurationFileRecord();
        if ($record->exists()) {
            $xml = simplexml_load_file($record->getFile());
            if ($xml->webfonts) {
                foreach ($xml->webfonts->skin as $skinNode) {
                    if ((string) $skinNode['identifier'] == $skinIdentifier) {
                        foreach ($skinNode->font as $fontNode) {
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
