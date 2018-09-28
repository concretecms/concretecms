<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Object\EntryInterface;

interface ObjectRegistryInterface extends RegistryInterface
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
