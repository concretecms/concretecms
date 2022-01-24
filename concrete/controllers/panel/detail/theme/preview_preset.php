<?php
namespace Concrete\Controller\Panel\Detail\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key as PermissionKey;

class PreviewPreset extends BackendInterfaceController
{
    protected $viewPath = '/panels/details/theme/preview_preset';

    public function canAccess()
    {
        $pk = PermissionKey::getByHandle('customize_themes');
        return $pk->validate();
    }

    public function view($pThemeID, $presetIdentifier, $pageID)
    {
        $theme = Theme::getByID($pThemeID);
        if (!$theme->isThemeCustomizable()) {
            throw new \RuntimeException(t('Theme %s is not customizable', $theme->getThemeHandle()));
        }
        $page = Page::getByID($pageID);
        $this->set('previewPage', $page);
        $this->set('pThemeID', $pThemeID);
        $this->set('presetIdentifier', $presetIdentifier);
        $this->set('token', $this->app->make('token'));
        $this->set('customizer', $theme->getThemeCustomizer());
    }

    public function viewIframe($pThemeID, $presetIdentifier, $pageID)
    {
        if ($this->app->make('token')->validate()) {
            $page = Page::getByID($pageID);
            $checker = new Checker($page);
            if ($checker->canViewPage()) {
                $theme = Theme::getByID($pThemeID);
                $customizer = $theme->getThemeCustomizer();
                $preset = $customizer->getPresetByIdentifier($presetIdentifier);
                $previewHandler = $customizer->getType()->getPreviewHandler();
                if ($this->request->request->has('styles')) {
                    // This is a preview request with custom, changed style data. Let's parse
                    // that data and compile it into a temporary CSS file.
                    $response = $previewHandler->getCustomPreviewResponse($customizer, $page, $preset, $this->request->request->all());
                } else {
                    $response = $previewHandler->getPresetPreviewResponse($customizer, $page, $preset);
                }
                return $response;
            }
        }
        throw new UserMessageException(t('Access Denied'));
    }

}
