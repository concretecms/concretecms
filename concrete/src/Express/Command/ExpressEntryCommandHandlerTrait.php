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
            if ($associationEntries === []) {
                $this->applier->removeAssociation($association, $entry);
            } elseif ($association instanceof ManyToManyAssociation) {
                $this->applier->associateManyToMany($association, $entry, $associationEntries);
            } elseif ($association instanceof OneToManyAssociation) {
                $this->applier->associateOneToMany($association, $entry, $associationEntries);
            } elseif ($association instanceof ManyToOneAssociation) {
                $this->applier->associateManyToOne($association, $entry, $associationEntries[0]);
            } elseif ($association instanceof OneToOneAssociation) {
                $this->applier->associateOneToOne($association, $entry, $associationEntries[0]);
            }
        }
    }


}
