<?php
namespace Concrete\Controller\Panel\Theme;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Adapter\AdapterFactory;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;

class CustomizeSkin extends BackendInterfacePageController
{
    protected $viewPath = '/panels/theme/customize';
    protected $controllerActionPath = '/panels/theme/customize';

    public function canAccess()
    {
        $page = Page::getByPath('/dashboard/pages/themes');
        $checker = new Checker($page);
        return $checker->canViewPage();
    }

    public function view($pThemeID, $skinIdentifier, $previewPageID)
    {
        $previewPage = Page::getByID($previewPageID);
        $checker = new Checker($previewPage);
        if ($checker->canViewPage()) {
            $theme = Theme::getByID($pThemeID);
            if ($theme) {
                $skin = $theme->getSkinByIdentifier($skinIdentifier);
                if ($skin) {
                    $styleList = $theme->getThemeCustomizableStyleList();
                    $adapterFactory = $this->app->make(AdapterFactory::class);
                    $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                    $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
                    $adapter = $adapterFactory->createFromTheme($theme);
                    $variableCollection = $variableCollectionFactory->createVariableCollectionFromSkin($adapter, $skin);
                    $valueList = $styleValueListFactory->createFromVariableCollection($styleList, $variableCollection);
                    $groupedStyleValueList = $valueList->createGroupedStyleValueList($styleList);
                    $this->set('styles', $groupedStyleValueList);
                    $this->set('skins', $skin);
                    $this->set('pThemeID', $pThemeID);
                    $this->set('skinIdentifier', $skinIdentifier);
                    $this->set('previewPage', $previewPage);
                    return;
                }
            }
        }
        throw new \RuntimeException(t('Invalid theme ID, preview page or skin identifier.'));
    }



}
