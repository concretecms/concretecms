<?php
namespace Concrete\Core\Permission\Registry;

interface RegistryInterface
{

    /**
     * @return EntryInterface[]
     */
    function getEntries();

}
