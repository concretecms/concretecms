<?php
namespace Concrete\Core\Express\Entry;


use Concrete\Core\Entity\Express\Entry;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Listener
{

    public function preRemove(Entry $entry, LifecycleEventArgs $event)
    {
        $db = $event->getEntityManager()->getConnection();

        // Delete any express entry attributes that have this selected.
        $db->Execute('delete from atExpressSelectedEntries where exEntryID = ?', array($entry->getID()));

        $category = \Core::make('Concrete\Core\Attribute\Category\ExpressCategory');

        foreach($category->getAttributeValues($entry) as $value) {
            $category->deleteValue($value);
        }

    }



}