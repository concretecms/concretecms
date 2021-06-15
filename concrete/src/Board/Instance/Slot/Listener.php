<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Board\InstanceSlot;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Listener
{
    public function preRemove(InstanceSlot $instanceSlot, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $bID = $instanceSlot->getBlockID();
        if ($bID) {
            $b = Block::getByID($bID);
            if ($b) {
                // Direct queries are the only way here because of the way the block class works
                $db = $event->getEntityManager()->getConnection();
                $db->delete('CollectionVersionBlocks', ['bID' => $b->getBlockID()]);
                $db->delete('BlockPermissionAssignments', ['bID' => $b->getBlockID()]);
                $db->delete('CollectionVersionBlockStyles', ['bID' => $b->getBlockID()]);
                $db->delete('CollectionVersionBlocksCacheSettings', ['bID' => $b->getBlockID()]);
                $bc = $b->getController();
                $bc->delete();
            }
        }
    }
}


