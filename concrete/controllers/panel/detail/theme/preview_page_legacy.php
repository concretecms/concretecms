<?php
namespace Concrete\Controller\Panel\Detail\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key as PermissionKey;

class PreviewPageLegacy extends BackendInterfaceController
{
    protected $viewPath = '/panels/details/theme/preview_page_legacy';

    public function canAccess()
    {
        $pk = PermissionKey::getByHandle('customize_themes');
        return $pk->validate();
    }

    public function view($pThemeID, $pageID)
    {
        $theme = Theme::getByID($pThemeID);
        if (!$theme->isThemeCustomizable()) {
            throw new \RuntimeException(t('Theme %s is not customizable', $theme->getThemeHandle()));
        }
        $page = Page::getByID($pageID);
        $this->set('previewPage', $page);
        $this->set('pThemeID', $pThemeID);
        $this->set('token', $this->app->make('token'));
        $this->set('customizer', $theme->getThemeCustomizer());
    }

    public function viewIframe($pThemeID, $pageID)
    {
        if ($this->app->make('token')->validate()) {
            $page = Page::getByID($pageID);
            $checker = new Checker($page);
            if ($checker->canViewPage()) {
                $theme = Theme::getByID($pThemeID);
                $customizer = $theme->getThemeCustomizer();
                $previewHandler = $customizer->getType()->getPreviewHandler();
                $type = $customizer->getType();
                $manager = $type->getCustomizationsManager();
                if ($this->request->request->has('styles')) {

                    // Nothing here yet. Maybe never?

                } else {
                    $customStyle = $manager->getCustomStyleObjectForPage($page, $theme);
                    if ($customStyle) {
                        $response = $previewHandler->getCustomStylePreviewResponse($customizer, $page, $customStyle);
                    }
                }
                return $response;
            }
        }
        throw new UserMessageException(t('Access Denied'));
    }

}
