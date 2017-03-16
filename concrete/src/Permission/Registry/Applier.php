<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\EntryInterface;
use Concrete\Core\Permission\Registry\Entry\EntrySubjectInterface;
use Core;

class Applier
{

    public function apply(EntrySubjectInterface $subject, RegistryInterface $registry)
    {
        Core::make('cache/request')->disable();
        foreach($registry->getEntries() as $entry) {
            $entry->apply($subject);
        }
        foreach($registry->getEntriesToRemove() as $entry) {
            $entry->remove($subject);
        }
        Core::make('cache/request')->enable();
    }

    public function applyAssignment(AssignmentInterface $assignment)
    {
        $this->apply($assignment->getEntry(), $assignment->getRegistry());
    }

    public function applyAssignmentList(AssignmentListInterface $list)
    {
        foreach($list->getAssignments() as $assignment) {
            $this->applyAssignment($assignment);
        }
    }

}