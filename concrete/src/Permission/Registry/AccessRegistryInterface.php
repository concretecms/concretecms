<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Access\EntryInterface;
use Concrete\Core\Permission\Registry\Entry\Access\AccessEntryInterface;

interface AccessRegistryInterface extends RegistryInterface
{

    /**
     * @return EntryInterface[]
     */
    function getEntries();

    /**
     * @return EntryInterface[]
     */
    function getEntriesToRemove();

}
