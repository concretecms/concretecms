<?php
namespace Concrete\Controller\Dialog\Express;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Search\Express\Entries;
use Concrete\Controller\Element\Dashboard\Express\Entries\Header;

/**
 * @since 8.0.0
 */
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

    /**
     * @since 8.1.0
     */
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
        $header = new Header($this->getEntity());
        $this->set('headerMenu', $header);
        $this->set('result', $result);
        $this->set('searchController', $search);
    }
}
