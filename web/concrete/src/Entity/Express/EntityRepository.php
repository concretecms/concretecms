<?php
namespace Concrete\Core\Entity\Express;

use Concrete\Core\Tree\Node\Type\ExpressEntryResults;

class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByIncludeInPublicList($find = null)
    {
        if ($find === null) {
            return $this->findBy(array());
        } else {
            $find = $find ? true : false;
        }
        return $this->findBy(array('include_in_public_list' => $find));
    }

    public function findOneByResultsNode(ExpressEntryResults $node)
    {
        return $this->findOneBy(array('entity_results_node_id' => $node->getTreeNodeID()));
    }

}
