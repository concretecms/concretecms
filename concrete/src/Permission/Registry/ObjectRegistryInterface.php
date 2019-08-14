<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Object\EntryInterface;

/**
 * @since 8.0.0
 */
interface ObjectRegistryInterface extends RegistryInterface
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
