<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Concrete\Core\Api\Express\Association\AssociationMap;
use Concrete\Core\Api\Attribute\AttributeValueMap;

trait ExpressEntryCommandHandlerTrait
{

    public function handleAttributeMap(AttributeValueMap $map, Entry $entry)
    {
        foreach ($map->getEntries() as $mapAttribute) {
            $key = $mapAttribute->getAttributeKey();
            $value = $mapAttribute->getAttributeValue();
            $entry->setAttribute($key, $value);
        }
    }

    public function handleAssociationMap(AssociationMap $map, Entry $entry)
    {
        foreach ($map->getEntries() as $mapAssociation) {
            $association = $mapAssociation->getAssociation();
            $associationEntries = $mapAssociation->getEntries();
            if (is_array($associationEntries)) {
                if ($association instanceof ManyToManyAssociation) {
                    $this->applier->associateManyToMany($association, $entry, $associationEntries);
                } else if ($association instanceof OneToManyAssociation) {
                    $this->applier->associateOneToMany($association, $entry, $associationEntries);
                } else if ($association instanceof ManyToOneAssociation) {
                    $this->applier->associateManyToOne($association, $entry, $associationEntries[0]);
                } else if ($association instanceof OneToOneAssociation) {
                    $this->applier->associateOneToOne($association, $entry, $associationEntries[0]);
                }
            } else if (is_null($associationEntries)) {
                $this->applier->removeAssociation($association, $entry);
            }
        }
    }


}
