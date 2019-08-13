<?php
namespace Concrete\Core\Permission\Inheritance\Registry;

use Concrete\Core\Permission\Inheritance\Registry\Entry\EntryInterface;

/**
 * @since 8.0.0
 */
interface RegistryInterface
{

    /**
     * @return EntryInterface
     */
    function getEntry($pkCategoryHandle, $pkHandle);

}
