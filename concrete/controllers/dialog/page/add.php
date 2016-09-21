<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use PageType;

class Add extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/add';
    protected $frequentPageTypes = array();
    protected $otherPageTypes = array();

    protected function canAccess()
    {
        return $this->permissions->canAddSubpages();
    }

    public function view()
    {
        $tree = $this->page->getSiteTreeObject();
        $type = $tree->getSiteType();
        $frequentlyUsed = PageType::getFrequentlyUsedList($type);
        foreach ($frequentlyUsed as $pt) {
            if ($this->permissions->canAddSubCollection($pt) && $pt->canPublishPageTypeBeneathPage($this->page)) {
                $this->frequentPageTypes[] = $pt;
            }
        }

        $otherPageTypes = PageType::getInfrequentlyUsedList($type);
        foreach ($otherPageTypes as $pt) {
            if ($this->permissions->canAddSubCollection($pt) && $pt->canPublishPageTypeBeneathPage($this->page)) {
                $this->otherPageTypes[] = $pt;
            }
        }

        $this->set('frequentPageTypes', $this->frequentPageTypes);
        $this->set('otherPageTypes', $this->otherPageTypes);
    }
}
