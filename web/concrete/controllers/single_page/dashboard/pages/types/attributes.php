<?
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Types;
use \Concrete\Core\Page\Controller\DashboardPageController;
use PageType;

class Attributes extends DashboardPageController
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

}