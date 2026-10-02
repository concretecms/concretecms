<?php
namespace Concrete\Core\Permission\Inheritance\Registry;

use Concrete\Core\Permission\Inheritance\Registry\Entry\EntryInterface;

interface RegistryInterface
{

    /**
     * @param string $pkCategoryHandle
     * @param string $pkHandle
     *
     * @return EntryInterface|null
     */
    function getEntry($pkCategoryHandle, $pkHandle);

}
