<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Types;

use Concrete\Core\Page\Controller\DashboardPageController;
use PageTemplate;
use PageType;
use Redirect;
use Session;

class Output extends DashboardPageController
{
    public function view($ptID = false)
    {
        $this->pagetype = PageType::getByID($ptID);
        if (!$this->pagetype) {
            $this->redirect('/dashboard/pages/types');
        }
        $cmp = new \Permissions($this->pagetype);
        if (!$cmp->canEditPageType()) {
            throw new \Exception(t('You do not have access to edit this page type.'));
        }
        $this->set('pagetype', $this->pagetype);
    }

    public function edit_defaults($ptID = false, $pTemplateID = false)
    {
        $this->view($ptID);
        $template = PageTemplate::getByID($pTemplateID);
        if (!is_object($template)) {
            $this->redirect('/dashboard/pages/types');
        }
        $valid = false;
        foreach ($this->pagetype->getPageTypePageTemplateObjects() as $pt) {
            if ($pt->getPageTemplateID() == $template->getPageTemplateID()) {
                $valid = true;
                break;
            }
        }
        if (!$valid) {
            $this->error->add(t('Invalid page template.'));
        }
        if (!$this->error->has()) {
            // we load up the master template for this composer/template combination.
            $c = $this->pagetype->getPageTypePageTemplateDefaultPageObject($template);
            Session::set('mcEditID', $c->getCollectionID());
            Redirect::url(\URL::to($c))->send();
        }
    }
}
