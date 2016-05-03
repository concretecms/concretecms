<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Search\Field\ManagerFactory;
use FilePermissions;
use Loader;
use Concrete\Controller\Search\Files as SearchFilesController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdvancedSearch extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/file/advanced_search';

    protected function canAccess()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles()) {
            return true;
        } else {
            return false;
        }
    }

    public function view()
    {
        $manager = ManagerFactory::get('file');
        $this->set('manager', $manager);
    }

    public function addField()
    {
        $manager = ManagerFactory::get('file');
        $field = $this->request->request->get('field');
        $field = $manager->getFieldByKey($field);
        if (is_object($field)) {
            return new JsonResponse($field);
        }
    }

}
