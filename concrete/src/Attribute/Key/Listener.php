<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Tree\Node\Node;
use Doctrine\ORM\Event\LifecycleEventArgs;

class Listener
{

    public function preRemove(Key $key, LifecycleEventArgs $event)
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

        // Delete any attribute values found attached to this key
        $values = $category->getAttributeValueRepository()->findBy(['attribute_key' => $key]);
        foreach($values as $attributeValue) {
            $category->deleteValue($attributeValue);
        }
    }


}