<?php
namespace Concrete\Controller\SinglePage\Dashboard\Express;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Page\Controller\DashboardExpressEntriesPageController;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Tree\Node\Node;

class Entries extends DashboardExpressEntriesPageController
{

    /**
     * @var $entity Entity
     */
    protected $entity;

    protected function getResultsTreeNodeObject()
    {
        return Node::getByID($this->entity->getEntityResultsNodeId());
    }

    protected function getBackToListURL(Entry $entry)
    {
        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'view', $entry->getEntity()->getID(),
            $entry->getEntity()->getEntityResultsNodeID());
    }

    protected function getCreateEntryURL()
    {
        return \URL::to('/dashboard/express/create', $this->entity->getId());
    }

    public function view($entity = null, $folder = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        if ($entity) {
            $entity = $r->findOneById($entity);
        }
        if (isset($entity) && is_object($entity)) {
            $this->entity = $entity;
            $this->set('entity', $entity);
            $this->renderList($folder);
        } else {
            $this->set('entities', $r->findByIncludeInPublicList(true));
        }
    }


}
