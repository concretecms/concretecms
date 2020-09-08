<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Formatter\ExpressEntryResultsListFormatter;
use Concrete\Core\Tree\Node\Type\Menu\ExpressEntryResultsFolderMenu;
use Doctrine\ORM\EntityManager;
use Loader;

class ExpressEntryResults extends ExpressEntryCategory
{

    public function getTreeNodeMenu()
    {
        return new ExpressEntryResultsFolderMenu($this);
    }

    public function getTotalResultsInFolder()
    {
        $em = \Database::connection()->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(e.exEntryID)')
            ->from('\Concrete\Core\Entity\Express\Entry', 'e')
            ->where('e.resultsNodeID = :resultsNodeID');
        $qb->setParameter('resultsNodeID', $this->getTreeNodeID());
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getSiteResultsNode(Site $site)
    {
        $db = app('database')->connection();
        $row = $db->fetchAssoc('select * from TreeExpressEntrySiteResultNodes tsn inner join TreeNodes tn 
          on tsn.treeNodeID = tn.treeNodeID where tn.treeNodeParentID = ? and tsn.siteID = ?', [
              $this->getTreeNodeID(), $site->getSiteID()
          ]);

        if ($row && $row['treeNodeID']) {
            return ExpressEntrySiteResults::getByID($row['treeNodeID']);
        }
    }

    public function getEntity()
    {
        $r = app(EntityManager::class)->getRepository(Entity::class);
        $entity = $r->findOneByResultsNode($this);
        return $entity;
    }

}
