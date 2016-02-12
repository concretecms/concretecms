<?php
namespace Concrete\Controller\Dialog\Express;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Search\Express\Entries;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/express/entry/search';

    protected function canAccess()
    {
        $c = \Page::getByPath('/dashboard/express/entities');
        $cp = new \Permissions($c);
        return $cp->canViewPage();
    }

    public function entries($entityID)
    {
        $em = \Database::connection()->getEntityManager();
        $entity = $em->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneById($entityID);
        $search = new Entries();
        $search->search($entity);

        $result = json_encode($search->getSearchResultObject()->getJSONObject());
        $this->set('result', $result);
        $this->set('searchController', $search);
    }
}
