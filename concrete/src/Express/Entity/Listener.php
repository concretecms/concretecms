<?php
namespace Concrete\Core\Express\Entity;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Tree\Node\Node;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Listener
{

    public function preRemove(Entity $entity, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $db = $em->getConnection();

        $db->Execute('delete from atExpressSettings where exEntityID = ?', array($entity->getID()));

        $entity->setDefaultEditForm(null);
        $entity->setDefaultViewForm(null);
        $em->persist($entity);

        foreach($entity->getForms() as $form) {
            $em->remove($form);
        }

        $em->flush();

        // Delete the keys
        $category = $entity->getAttributeKeyCategory();
        foreach($category->getList() as $key) {
            $em->remove($key);
        }

        $em->flush();

        $list = new EntryList($entity);
        foreach($list->getResults() as $result) {
            $em->remove($result);
        }

        $em->flush();

    }


    public function postRemove(Entity $entity, LifecycleEventArgs $event)
    {
        $node = Node::getByID($entity->getEntityResultsNodeId());
        if (is_object($node)) {
            $node->delete();
        }
    }


}