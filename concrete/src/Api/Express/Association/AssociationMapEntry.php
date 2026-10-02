<?php

namespace Concrete\Core\Api\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;

class AssociationMapEntry
{

    /**
     * @var Association
     */
    protected $association;

    /**
     * @var \Concrete\Core\Entity\Express\Entry[] an empty list removes the association
     */
    protected $entries;

    /**
     * @param \Concrete\Core\Entity\Express\Entry[] $entries the entries to be associated (an empty list removes the association)
     */
    public function __construct(Association $association, array $entries)
    {
        $this->association = $association;
        $this->entries = $entries;
    }

    public function getAssociation(): Association
    {
        return $this->association;
    }

    /**
     * @return \Concrete\Core\Entity\Express\Entry[] the entries to be associated (an empty list removes the association)
     */
    public function getEntries(): array
    {
        return $this->entries;
    }



}