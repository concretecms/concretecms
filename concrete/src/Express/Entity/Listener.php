<?php
namespace Concrete\Core\Express\Entity;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\ExpressEntryResults as ExpressEntryResultsTree;
use Concrete\Core\Tree\Node\Type\ExpressEntryResults as ExpressEntryResultsNode;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Listener
{

    public function preRemove(Entity $entity, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $entity->setDefaultEditForm(null);
        $entity->setDefaultViewForm(null);
        $em->persist($entity);

        foreach ($entity->getForms() as $form) {
            $em->remove($form);
        }

        $em->flush();

        // Delete the keys
        $category = $entity->getAttributeKeyCategory();
        foreach ($category->getList() as $key) {
            $em->remove($key);
        }

        $em->flush();

        try {
            $list = new EntryList($entity);
            foreach ($list->getResults() as $result) {
                $em->remove($result);
            }
        } catch (\Exception $e) {
        }

        $em->flush();

    }

    public function prePersist(Entity $entity, LifecycleEventArgs $event)
    {
        if (!$entity->getEntityResultsNodeId()) {
            // Create a results node
            $tree = ExpressEntryResultsTree::get();
            if (is_object($tree)) {
                $node = $tree->getRootTreeNodeObject();
                $node = ExpressEntryResultsNode::add($entity->getName(), $node);
                $entity->setEntityResultsNodeId($node->getTreeNodeID());
            }
        }

        $indexer = $entity->getAttributeKeyCategory()->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->createRepository($entity->getAttributeKeyCategory());
        }

    }

    public function postRemove(Entity $entity, LifecycleEventArgs $event)
    {
        $node = Node::getByID($entity->getEntityResultsNodeId());
        if (is_object($node)) {
            $node->delete();
        }
    }


}