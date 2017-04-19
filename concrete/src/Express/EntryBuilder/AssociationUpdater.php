<?php
namespace Concrete\Core\Express\EntryBuilder;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Association\Applier;

class AssociationUpdater
{

    protected $applier;
    protected $entry;

    public function __construct(Applier $applier, Entry $entry)
    {
        $this->applier = $applier;
        $this->entry = $entry;
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            $method = preg_replace('/(?!^)[[:upper:]]/', '_\0', $method);
            $method = strtolower($method);
            $identifier = str_replace('set_', '', $method);
            $this->associate($identifier, $arguments[0]);
        }
        return $this;
    }

    public function associate($associationHandle, $input)
    {
        $association = $this->entry->getEntity()->getAssociation($associationHandle);
        $this->applier->associate($association, $this->entry, $input);
    }


}
