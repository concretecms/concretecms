<?php
namespace Concrete\Core\Permission\Registry;

/**
 * @since 8.0.0
 */
interface AssignmentListInterface
{

    /**
     * @return AssignmentInterface[]
     */
    public function getAssignments();

    

}