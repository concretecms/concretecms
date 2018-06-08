<?php
namespace Concrete\Core\Express\Entry;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Listener
{
    public function preRemove(Entry $entry, LifecycleEventArgs $event)
    {
        $app = Facade::getFacadeApplication();
        $express = $app->make('express');
        $associations = $entry->getAssociations();
        foreach ($associations as $entryAssociation) {
            /**
             * @var $entryAssociation Entry\Association
             */
            if ($entryAssociation->getAssociation()->isOwningAssociation()) {
                $associatedEntries = $entryAssociation->getSelectedEntries();
                if ($associatedEntries) {
                    foreach ($associatedEntries as $associatedEntry) {
                        $express->deleteEntry($associatedEntry->getId());
                    }
                }
            }
        }
        $db = $event->getEntityManager()->getConnection();

        // Delete any express entry attributes that have this selected.
        $db->Execute('delete from atExpressSelectedEntries where exEntryID = ?', array($entry->getID()));

        // Delete this from any associations that reference it
        $db->Execute('delete from ExpressEntityAssociationEntries where exEntryID = ?', array($entry->getID()));

        $category = \Core::make('Concrete\Core\Attribute\Category\ExpressCategory');

        foreach ($category->getAttributeValues($entry) as $value) {
            $category->deleteValue($value);
        }
    }
}
