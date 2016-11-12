<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\EntryInterface;
use Concrete\Core\Permission\Registry\Entry\EntrySubjectInterface;

interface AssignmentInterface
{

    /**
     * @return EntrySubjectInterface
     */
    function getEntry();

    /**
     * @return RegistryInterface
     */
    function getRegistry();

    

}