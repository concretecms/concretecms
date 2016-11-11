<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\EntryInterface;
use Concrete\Core\Permission\Registry\Entry\EntrySubjectInterface;

class Applier
{

    public function apply(EntrySubjectInterface $subject, RegistryInterface $registry)
    {
        foreach($registry->getEntries() as $entry) {
            $entry->apply($subject);
        }
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