<?php
namespace Concrete\Core\Permission\Inheritance\Registry;

use Concrete\Core\Permission\Inheritance\Registry\Entry\EntryInterface;

abstract class AbstractRegistry implements RegistryInterface
{

    /**
     * @var EntryInterface[]
     */
    protected $entries = [];

    public function addEntry(EntryInterface $entry)
    {
        $this->entries[] = $entry;
    }

    public function getEntry($pkCategoryHandle, $pkHandle)
    {
        foreach($this->entries as $entry) {
            if ($entry->getInheritedFromPermissionKeyCategoryHandle() == $pkCategoryHandle && $pkHandle == $entry->getPermissionKeyHandle()) {
                return $entry;
            }
        }
    }

}
