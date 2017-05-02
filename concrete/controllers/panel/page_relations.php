<?php
namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Multilingual\Page\Section\Section;
use Core;

class PageRelations extends BackendInterfacePageController
{
    protected $viewPath = '/panels/page/relations';

    public function view()
    {
        $this->requireAsset('core/sitemap');
        $mlist = Section::getList();
        $ml = array();
        $currentSection = Section::getCurrentSection();
        foreach ($mlist as $m) {
            if ($m->getCollectionID() != $currentSection->getCollectionID()) {
                $ml[] = $m;
            }
        }
        $this->set('multilingualSectionList', $ml);
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
        $dashboard = $app->make('helper/concrete/dashboard');

        if (
            $app->make('multilingual/detector')->isEnabled()
            && is_object(Section::getCurrentSection())
            && !$dashboard->inDashboard($this->page)
            && $this->permissions->canEditPageMultilingualSettings()) {

            return true;

        }

        /*

        if (!$dashboard->inDashboard($this->page) && count($this->page->getPageRelations()) > 0) {
            return true;
        }

        */

        return false;
    }

}
