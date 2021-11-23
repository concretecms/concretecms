<?php
namespace Concrete\Controller\Panel\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class Customize extends BackendInterfaceController
{
    protected $viewPath = '/panels/theme/skins';
    protected $controllerActionPath = '/ccm/panels/theme';

    public function canAccess()
    {
        $pk = PermissionKey::getByHandle('customize_themes');
        return $pk->validate();
    }

    public function view($pThemeID, $previewPageID)
    {
        $theme = Theme::getByID($pThemeID);
        if ($theme) {
            $customizer = $theme->getThemeCustomizer();
            if ($customizer) {
                $this->set('customizer', $customizer);
                $this->set('theme', $theme);
                $previewPage = Page::getByID($previewPageID);
                $checker = new Checker($previewPage);
                if ($checker->canEditPageTheme()) {
                    $previewPage = Page::getByID($previewPageID);
                    $this->set('previewPage', $previewPage);
                    $activeSkin = SkinInterface::SKIN_DEFAULT;
                    $site = $previewPage->getSite();
                    if (!$site) {
                        // We must be doing something like previewing a documentation page that has no site
                        $site = $this->app->make('site')->getActiveSiteForEditing();
                    }
                    if ($site->getThemeSkinIdentifier()) {
                        $activeSkin = $site->getThemeSkinIdentifier();
                    }
                    $this->set('activeSkin', $activeSkin);
                }
            }
        }
    }


}
