<?php
namespace Concrete\Core\Express\Entity;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\ExpressEntryResults as ExpressEntryResultsTree;
use Concrete\Core\Tree\Node\Type\ExpressEntryResults as ExpressEntryResultsNode;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Concrete\Core\Entity\Express\Entry;

class Listener
{

    public function preRemove(Entity $entity, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $db = $em->getConnection();

        $db->Execute('delete from atExpressSettings where exEntityID = ?', array($entity->getID()));
        $table = $entity->getAttributeKeyCategory()->getIndexedSearchTable();
        $db->Execute('DROP TABLE IF EXISTS ' . $table);

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

        $entryRepository = $em->getRepository(Entry::class);
        $entries = $entryRepository->findByEntity($entity);
        foreach($entries as $result) {
            $em->remove($result);
        }

        // Delete the associations.
        foreach ($entity->getAssociations() as $association) {
            $em->remove($association);
        }

        // Make sure to delete the inverse associations
        $associations = $em->getRepository('Concrete\Core\Entity\Express\Association')
            ->findBy(['target_entity' => $entity]);
        foreach ($associations as $association) {
            $em->remove($association);
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