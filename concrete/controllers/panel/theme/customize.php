<?php
namespace Concrete\Controller\Panel\Theme;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;

class Customize extends BackendInterfacePageController
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
            $this->set('skins', $theme->getSkins());
            $previewPage = Page::getByID($previewPageID);
            $checker = new Checker($previewPage);
            if ($checker->canViewPage()) {
                $previewPage = Page::getByID($previewPageID);
                $this->set('previewPage', $previewPage);
            }
        }
    }


}
