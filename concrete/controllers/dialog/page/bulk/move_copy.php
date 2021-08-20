<?php
namespace Concrete\Controller\Dialog\Page\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Page;
use Permissions;

class MoveCopy extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/page/sitemap_selector';
    protected $pages;
    protected $canEdit = false;

    protected function canAccess()
    {
        $this->populatePages();

        return $this->canEdit;
    }

    protected function populatePages()
    {
        if (!isset($this->pages)) {
            if (is_array($_REQUEST['item'])) {
                foreach ($_REQUEST['item'] as $cID) {
                    $c = Page::getByID($cID);
                    if (is_object($c) && !$c->isError()) {
                        $this->pages[] = $c;
                    }
                }
            }
        }

        if (count($this->pages) > 0) {
            $this->canEdit = true;
            foreach ($this->pages as $c) {
                $cp = new Permissions($c);
                if (!$cp->canDeletePage()) {
                    $this->canEdit = false;
                }
            }
        } else {
            $this->canEdit = false;
        }

        return $this->canEdit;
    }

    public function view()
    {
        $this->populatePages();
        $this->set('selectMode', "move_copy_delete");
        $this->set('uniqid', uniqid());
        $this->set('includeSystemPages', $this->request->query->get('includeSystemPages') ? true : false);
        $this->set('askIncludeSystemPages', $this->request->query->get('askIncludeSystemPages') ? true : false);
        $this->set('pages', $this->pages);
    }

}
