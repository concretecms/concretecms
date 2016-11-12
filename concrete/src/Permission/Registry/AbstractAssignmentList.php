<?php
namespace Concrete\Core\Permission\Registry;

abstract class AbstractAssignmentList implements AssignmentListInterface
{

    protected $assignments = [];

    public function addAssignment(AssignmentInterface $assignment)
    {
        $this->assignments[] = $assignment;
    }

    public function getAssignments()
    {
        return $this->assignments;
    }

}