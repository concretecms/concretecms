<?php
namespace Concrete\Core\Permission\Inheritance\Registry;

use Concrete\Core\Permission\Inheritance\Registry\Entry\EntryInterface;

interface RegistryInterface
{

    /**
     * @return EntryInterface
     */
    function getEntry($pkCategoryHandle, $pkHandle);

}
