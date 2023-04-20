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
     * @var Entry[]
     */
    protected $entries;

    /**
     * AssociationMapEntry constructor.
     * @param Association $association
     * @param Entry[] $entries
     */
    public function __construct(Association $association, array $entries)
    {
        $this->association = $association;
        $this->entries = $entries;
    }

    /**
     * @return Association
     */
    public function getAssociation(): Association
    {
        return $this->association;
    }

    /**
     * @return Entry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }



}