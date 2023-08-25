<?php

namespace Concrete\Core\User\Group\Command\DeleteGroupCommand;

final class Result
{
    /**
     * @var int[]
     */
    private $deletedGroupIDs = [];

    /**
     * Array keys are the group IDs, array values are the reasons why the group couldn't be deleted.
     */
    private $undeletableGroups = [];

    public function __toString(): string
    {
        $lines = [];
        $lines[] = t2(/*i18n: %s is a number*/ '%s group has been deleted', '%s groups have been deleted', $this->getNumberOfDeletedGroups());
        foreach ($this->getUndeletableGroups() as $reason) {
            $lines[] = $reason;
        }

        return implode("\n", $lines);
    }

    public function isGroupDeleted(int $groupID): bool
    {
        return in_array($groupID, $this->deletedGroupIDs, true);
    }

    /**
     * @return int[]
     */
    public function getDeletedGroupIDs(): array
    {
        return $this->deletedGroupIDs;
    }

    public function getNumberOfDeletedGroups(): int
    {
        return count($this->deletedGroupIDs);
    }

    /**
     * @return $this
     */
    public function addDeletedGroup(int $groupID): object
    {
        if (!in_array($groupID, $this->deletedGroupIDs, true)) {
            $this->deletedGroupIDs[] = $groupID;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addUndeletableGrup(int $groupID, string $reason): object
    {
        $this->undeletableGroups[$groupID] = $reason;

        return $this;
    }

    public function getNumberOfUndeletableGroups(): int
    {
        return count($this->undeletableGroups);
    }

    /**
     * Array keys are the group IDs, array values are the reasons why the group couldn't be deleted.
     */
    public function getUndeletableGroups(): array
    {
        return $this->undeletableGroups;
    }

    /**
     * Merge another Result instance into this one.
     *
     * @return $this
     */
    public function merge(self $other): object
    {
        $this->deletedGroupIDs = array_values(array_unique(array_merge($this->deletedGroupIDs, $other->getDeletedGroupIDs()), SORT_NUMERIC));
        $this->undeletableGroups += $other->getUndeletableGroups();

        return $this;
    }
}
