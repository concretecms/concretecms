<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\EntryInterface;
use Concrete\Core\Permission\Registry\Entry\EntrySubjectInterface;

/**
 * @since 8.0.0
 */
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