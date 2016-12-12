<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Panel\Page\Attributes as PageAttributesPanelController;
use Concrete\Controller\Panel\Detail\Page\Attributes as PageAttributesPanelDetailController;
use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;

class Attributes extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/attributes';

    protected function canAccess()
    {
        return $this->permissions->canEditPageProperties();
    }

    public function view()
    {
        $list = new PageAttributesPanelController();
        $list->setPageObject($this->page);
        $list->view();
        $this->set('menu', $list->getViewObject());

        $detail = new PageAttributesPanelDetailController();
        $detail->on_start();
        $detail->setPageObject($this->page);
        $detail->view();
        $detail = $detail->getViewObject();
        $detail->addScopeItems(array('sitemap' => true));
        $this->set('detail', $detail);
    }
}
