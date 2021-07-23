<?php
namespace Concrete\Core\StyleCustomizer\Compiler;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Adapter\AdapterInterface;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Skin\PresetSkin;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class Compiler
{

    /**
     * @param AdapterInterface $adapter
     * @param PresetSkin $presetSkin
     * @param NormalizedVariableCollection $collection
     * @return string
     */
    public function compileFromSkin(AdapterInterface $adapter, SkinInterface $skin, NormalizedVariableCollection $collection): string
    {
        $processor = $adapter->getProcessor();
        if ($skin instanceof PresetSkin) {
            $presetSkin = $skin;
        } else {
            /**
             * @var $skin CustomSkin
             */
            $theme = $skin->getTheme();
            $presetSkin = $theme->getSkinByIdentifier($skin->getPresetSkinStartingPoint());
        }
        $file = $adapter->getPresetEntryPointFile($presetSkin);
        $css = $processor->compileFileToString($file, $collection);
        return $css;
    }


}
