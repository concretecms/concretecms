<?php

namespace Concrete\Core\StyleCustomizer\WebFont;

use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class WebFontCollectionFactory
{

    /**
     * @param SkinInterface $skin
     * @return WebFontCollection
     */
    public function createFromSkin(SkinInterface $skin): WebFontCollection
    {
        $collection = new WebFontCollection();
        $record = $skin->getTheme()->getStyleConfigurationFileRecord();
        if ($record->exists()) {
            $xml = simplexml_load_file($record->getFile());
            if ($xml->webfonts) {
                foreach ($xml->webfonts->skin as $skinNode) {
                    if ((string) $skinNode['identifier'] == $skin->getIdentifier()) {
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
