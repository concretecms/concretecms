<?php
namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Multilingual\Page\Section\Section;
use Core;

class PageRelations extends BackendInterfacePageController
{
    protected $viewPath = '/panels/page/relations';

    public function view()
    {
        $mlist = Section::getList();
        $ml = array();
        $currentSection = Section::getCurrentSection();
        foreach ($mlist as $m) {
            if ($m->getCollectionID() != $currentSection->getCollectionID()) {
                $ml[] = $m;
            }
        }
        $this->set('list', $ml);
        $this->set('currentSection', $currentSection);

        $this->set('ih', Core::make('multilingual/interface/flag'));
        $multilingualController = Core::make('\Concrete\Controller\Backend\Page\Multilingual');
        $multilingualController->setPageObject($this->page);
        $this->set('multilingualController', $multilingualController);
        $relations = $this->page->getPageRelations();
        $this->set('siblingRelations', $relations);
    }

    public function canAccessPanel()
    {
        return $this->canAccess();
    }

    protected function canAccess()
    {
        $app = \Core::make("app");
        $config = $app->make('config');
        if ($config->get('concrete.interface.panel.page_relations')) {
            return true;
        }

        $currentSection = Section::getCurrentSection();
        $dashboard = $app->make('helper/concrete/dashboard');

        if (
            $app->make('multilingual/detector')->isEnabled()
            && is_object($currentSection)
            && !$dashboard->inDashboard($this->page)
            && $this->permissions->canEditPageMultilingualSettings()) {

            return true;

        }

        return false;
    }

}
