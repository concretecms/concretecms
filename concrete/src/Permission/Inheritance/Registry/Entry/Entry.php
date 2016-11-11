<?php
namespace Concrete\Core\Permission\Inheritance\Registry\Entry;

class Entry implements EntryInterface
{

    protected $permissionKeyHandle;
    protected $inheritedFromPermissionKeyHandle;
    protected $inheritedFromPermissionKeyCategoryHandle;

    /**
     * AbstractEntry constructor.
     * @param $permissionKeyHandle
     * @param $inheritedFromPermissionKeyHandle
     * @param $inheritedFromPermissionKeyCategoryHandle
     */
    public function __construct(
        $inheritedFromPermissionKeyCategoryHandle,
        $inheritedFromPermissionKeyHandle,
        $permissionKeyHandle
    ) {
        $this->permissionKeyHandle = $permissionKeyHandle;
        $this->inheritedFromPermissionKeyHandle = $inheritedFromPermissionKeyHandle;
        $this->inheritedFromPermissionKeyCategoryHandle = $inheritedFromPermissionKeyCategoryHandle;
    }

    /**
     * @return mixed
     */
    public function getPermissionKeyHandle()
    {
        return $this->permissionKeyHandle;
    }

    /**
     * @return mixed
     */
    public function getInheritedFromPermissionKeyHandle()
    {
        return $this->inheritedFromPermissionKeyHandle;
    }

    /**
     * @return mixed
     */
    public function getInheritedFromPermissionKeyCategoryHandle()
    {
        return $this->inheritedFromPermissionKeyCategoryHandle;
    }



}
