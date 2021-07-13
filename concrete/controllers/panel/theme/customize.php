<?php
namespace Concrete\Controller\Panel\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class Customize extends BackendInterfaceController
{
    protected $viewPath = '/panels/theme/skins';
    protected $controllerActionPath = '/ccm/panels/theme';

    public function canAccess()
    {
        $page = Page::getByPath('/dashboard/pages/themes');
        $checker = new Checker($page);
        return $checker->canViewPage();
    }

    public function view($pThemeID, $previewPageID)
    {
        $theme = Theme::getByID($pThemeID);
        if ($theme) {
            $this->set('theme', $theme);
            $this->set('presetSkins', $theme->getPresetSkins());
            $this->set('customSkins', $theme->getCustomSkins());
            $previewPage = Page::getByID($previewPageID);
            $checker = new Checker($previewPage);
            if ($checker->canViewPage()) {
                $previewPage = Page::getByID($previewPageID);
                $this->set('previewPage', $previewPage);
                $activeSkin = SkinInterface::SKIN_DEFAULT;
                $site = $previewPage->getSite();
                if ($site) {
                    if ($site->getThemeSkinIdentifier()) {
                        $activeSkin = $site->getThemeSkinIdentifier();
                    }
                }
                $this->set('activeSkin', $activeSkin);
            }
        }
    }


}
