<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\EntryInterface;

interface RegistryInterface
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
