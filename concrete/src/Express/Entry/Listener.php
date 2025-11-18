<?php
namespace Concrete\Core\Express\Entry;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Listener
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = app(LoggerFactory::class)->createLogger(Channels::CHANNEL_EXPRESS);
    }
    public function preRemove(Entry $entry, LifecycleEventArgs $event)
    {
        $this->logger->info(t('Deleting Express entry: %s (%s)', $entry->getLabel(), $entry->getID()));

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
    
    public function postRemove(Entry $entry, LifecycleEventArgs $event)
    {
        $this->logger->info(t('Express entry %s (%s) deleted successfully.', $entry->getLabel(), $entry->getID()));
    }
}
