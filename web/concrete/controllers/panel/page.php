<?php
namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;

class Page extends BackendInterfacePageController
{
    protected $viewPath = '/panels/page';

    public function canAccess()
    {
        $permissions = $this->permissions;

        return $permissions->canEditPageProperties() ||
            $permissions->canEditPageTheme() ||
            $permissions->canEditPageTemplate() ||
            $permissions->canDeletePage() ||
            $permissions->canEditPagePermissions();
    }

    public function view()
    {
    }
}
