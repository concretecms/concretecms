<?php

namespace Concrete\Core\User\Group\Command\Traits;

trait ExistingGroupTrait
{

    protected $groupID;

    /**
     * @return mixed
     */
    public function getGroupID()
    {
        return $this->groupID;
    }

    /**
     * @param mixed $groupID
     */
    public function setGroupID($groupID)
    {
        $this->groupID = $groupID;
    }



}