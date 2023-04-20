<?php

namespace Concrete\Core\Api\Express\Association;

/**
 * Used to join a command to a set of associations and their proposed entries.
 */
class AssociationMap
{

    /**
     * @var AssociationMapEntry[]
     */
    protected $entries = [];

    public function addEntry(AssociationMapEntry $entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * @return AssociationMapEntry[]|null
     */
    public function getEntries(): ?array
    {
        return $this->entries;
    }




}