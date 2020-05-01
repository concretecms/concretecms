<?php

namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\Key as AttributeKey;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Listener
{
    /**
     * @param \Concrete\Core\Entity\Attribute\Key\Key $key
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $event
     */
    public function preRemove(AttributeKey $key, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $category = $key->getAttributeCategory();

        // Remove the index column(s), if any
        if ($key->isAttributeKeySearchable()) {
            $indexer = $key->getSearchIndexer();
            if ($indexer instanceof SearchIndexerInterface) {
                $key->setIsAttributeKeySearchable(false);
                $indexer->updateSearchIndexKeyColumns($category, $key);
            }
        }
        // Delete the category key record
        $category->deleteKey($key);

        // take care of the settings
        $controller = $key->getController();
        $controller->deleteKey();

        // Delete from any attribute sets
        $r = $em->getRepository('\Concrete\Core\Entity\Attribute\SetKey');
        $setKeys = $r->findBy(['attribute_key' => $key]);
        foreach ($setKeys as $setKey) {
            $em->remove($setKey);
        }
    }
}
