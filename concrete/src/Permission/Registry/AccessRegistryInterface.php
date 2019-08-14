<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Access\EntryInterface;
use Concrete\Core\Permission\Registry\Entry\Access\AccessEntryInterface;

/**
 * @since 8.0.0
 */
interface AccessRegistryInterface extends RegistryInterface
{

    /**
     * @return EntryInterface[]
     */
    function getEntries();

    /**
     * @return EntryInterface[]
     * @since 8.2.0
     */
    function getEntriesToRemove();

}
