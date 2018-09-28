<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Formatter\ExpressEntryResultsListFormatter;
use Concrete\Core\Tree\Node\Type\Menu\ExpressEntryResultsFolderMenu;
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
        $entity = $em->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByResultsNode($this);
        if ($entity) {
            $qb = $em->createQueryBuilder();
            $qb->select('count(e.exEntryID)')
                ->from('\Concrete\Core\Entity\Express\Entry', 'e')
                ->where('e.entity = :entity');
            $qb->setParameter('entity', $entity);
            return $qb->getQuery()->getSingleScalarResult();
        }
    }


}
