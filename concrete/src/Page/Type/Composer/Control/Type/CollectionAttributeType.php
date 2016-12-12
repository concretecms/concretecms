<?php

namespace Concrete\Core\Page\Type\Composer\Control\Type;

use CollectionAttributeKey;
use Concrete\Core\Page\Type\Composer\Control\CollectionAttributeControl;
use Concrete\Core\Attribute\Key\Key as AttributeKey;

class CollectionAttributeType extends Type
{
    public function getPageTypeComposerControlObjects()
    {
        $objects = array();
        $keys = AttributeKey::getAttributeKeyList('collection');

        foreach ($keys as $ak) {
            $ac = new CollectionAttributeControl();
            $ac->setAttributeKeyID($ak->getAttributeKeyID());
            $ac->setPageTypeComposerControlIconFormatter($ak->getController()->getIconFormatter());
            $ac->setPageTypeComposerControlName($ak->getAttributeKeyDisplayName());
            $objects[] = $ac;
        }

        return $objects;
    }

    public function getPageTypeComposerControlByIdentifier($identifier)
    {
        $ak = CollectionAttributeKey::getByID($identifier);
        $ax = new CollectionAttributeControl();
        $ax->setAttributeKeyID($ak->getAttributeKeyID());
        $ax->setPageTypeComposerControlIconFormatter($ak->getController()->getIconFormatter());
        $ax->setPageTypeComposerControlName($ak->getAttributeKeyDisplayName());

        return $ax;
    }

    public function configureFromImportHandle($handle)
    {
        $ak = CollectionAttributeKey::getByHandle($handle);

        return static::getPageTypeComposerControlByIdentifier($ak->getAttributeKeyID());
    }
}
