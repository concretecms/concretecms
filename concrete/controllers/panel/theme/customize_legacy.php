<?php
namespace Concrete\Controller\Panel\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\StyleCustomizer\Customizer\Type\LegacyCustomizerType;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\CustomizerVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\StyleCustomizer\Traits\CustomizeControllerTrait;

/**
 * @deprecated
 */
class CustomizeLegacy extends BackendInterfaceController
{
    use CustomizeControllerTrait;

    protected $viewPath = '/panels/theme/customize';
    protected $controllerActionPath = '/panels/theme/customize';

    public function view($pThemeID, $previewPageID)
    {
        $this->loadPreviewPage($previewPageID);
        $customizer = $this->getCustomizer($pThemeID);
        $type = $customizer->getType();
        if ($type instanceof LegacyCustomizerType) {
            $manager = $type->getCustomizationsManager();
            if ($manager instanceof \Concrete\Core\StyleCustomizer\Customizations\LegacyCustomizationsManager) {
                $customStyle = $manager->getCustomStyleObjectForPage($this->get('previewPage'), $this->get('customizeTheme'));
                if ($customStyle) {
                    $presetIdentifier = $customStyle->getPresetHandle();
                    if (!$presetIdentifier) {
                        $presetIdentifier = 'defaults'; // custom page customizations may not have a preset starting point handle. But in the legacy customizer this doesn't really matter so any handle will do.
                    }
                    $preset = $customizer->getPresetByIdentifier($presetIdentifier);
                    $this->loadCustomizer($customizer, $preset, $customStyle);
                    $this->set('presetIdentifier', $presetIdentifier);
                }
            }
        } else {
            throw new \RuntimeException(
                t('The page-level customizer is only available to themes using the legacy customizer.')
            );
        }
    }


}
