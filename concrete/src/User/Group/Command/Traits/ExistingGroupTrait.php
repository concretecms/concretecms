<?php

namespace Concrete\Core\User\Group\Command\Traits;

trait ExistingGroupTrait
{
    /**
     * @var int
     */
    protected $groupID;

    /**
     * @return mixed
     */
    public function getGroupID(): int
    {
        return $this->groupID;
    }

    /**
     * @return $this
     */
    public function setGroupID(int $groupID): object
    {
        $this->groupID = $groupID;

        return $this;
    }
}
