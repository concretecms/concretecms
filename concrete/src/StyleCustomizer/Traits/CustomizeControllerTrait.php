<?php
namespace Concrete\Core\StyleCustomizer\Traits;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Page\CustomStyle;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\StyleCustomizer\Customizer\Customizer;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\CustomizerVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;

trait CustomizeControllerTrait
{

    public function canAccess()
    {
        $pk = PermissionKey::getByHandle('customize_themes');
        return $pk->validate();
    }

    protected function loadPreviewPage($previewPageID)
    {
        $previewPage = Page::getByID($previewPageID);
        $checker = new Checker($previewPage);
        if ($checker->canEditPageTheme()) {
            $this->set('previewPage', $previewPage);
        } else {
            throw new \RuntimeException(t('Unable to customize theme for page: %s', $previewPage->getCollectionID()));
        }
    }

    protected function getCustomizer($pThemeID)
    {
        $theme = Theme::getByID($pThemeID);
        if ($theme) {
            $this->set('customizeTheme', $theme);
            $this->set('pThemeID', $pThemeID);
            $customizer = $theme->getThemeCustomizer();
            if ($customizer) {
                return $customizer;
            } else {
                throw new \RuntimeException(t('Theme %s cannot be customized', $theme->getThemeHandle()));
            }
        } else {
            throw new \RuntimeException(t('Invalid theme: %s', h($pThemeID)));
        }
    }

    /**
     * @param Customizer $customizer
     * @param PresetInterface $preset
     * @param CustomStyle|SkinInterface|null $mixed
     */
    public function loadCustomizer(Customizer $customizer, PresetInterface $preset, $mixed = null)
    {
        $styleList = $customizer->getThemeCustomizableStyleList($preset);
        $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
        $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
        $customizerVariableCollectionFactory = $this->app->make(CustomizerVariableCollectionFactory::class);
        $customCss = null;
        if ($mixed instanceof CustomSkin) {
            $variableCollection = $variableCollectionFactory->createFromCustomSkin($mixed);
            $customCss = $mixed->getCustomCss();
        } else if ($mixed instanceof CustomStyle) {
            // legacy page customizer support.
            $valueList = $mixed->getValueList();
            if ($valueList) {
                $variableCollection = $variableCollectionFactory->createFromStyleValueList($valueList);
            }
            $customCssRecord = $mixed->getCustomCssRecord();
            if ($customCssRecord) {
                $customCss = $customCssRecord->getValue();
            }
        } else {
            $variableCollection = $variableCollectionFactory->createFromPreset($customizer, $preset);
        }
        $valueList = $styleValueListFactory->createFromVariableCollection($styleList, $variableCollection);
        $groupedStyleValueList = $valueList->createGroupedStyleValueList($styleList);
        $this->requireAsset('ace');
        $this->set('styleList', $groupedStyleValueList);
        $this->set('styles', $customizerVariableCollectionFactory->createFromStyleValueList($valueList));
        $this->set('preset', $preset);
        $this->set('customizer', $customizer);
        $this->set('customCss', $customCss);
    }
}
