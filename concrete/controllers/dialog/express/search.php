<?php
namespace Concrete\Controller\Dialog\Express;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Search\Express\Entries;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/express/entry/search';

    protected function canAccess()
    {
        $entity = $this->getEntity();
        if (is_object($entity)) {
            $ep = new \Permissions($entity);
            return $ep->canViewExpressEntries();
        }
        return false;
    }

    protected function getEntity()
    {
        $em = \Database::connection()->getEntityManager();
        return $em->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneById($this->request->get('exEntityID'));
    }

    public function entries()
    {
        $search = new Entries();
        $search->search($this->getEntity());
        $result = json_encode($search->getSearchResultObject()->getJSONObject());
        $this->set('result', $result);
        $this->set('searchController', $search);
    }
}
