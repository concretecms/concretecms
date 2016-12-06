<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Entity\Attribute\Key\Key as AttributeKey;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Tree\Node\Node;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Listener
{

    public function preRemove(AttributeKey $key, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $category = $key->getAttributeCategory();

        // Delete the category key record
        $category->deleteKey($key);

        // take care of the settings
        $controller = $key->getController();
        $controller->deleteKey();

        // Delete from any attribute sets
        $r = $em->getRepository('\Concrete\Core\Entity\Attribute\SetKey');
        $setKeys = $r->findBy(array('attribute_key' => $key));
        foreach ($setKeys as $setKey) {
            $em->remove($setKey);
        }
    }


}