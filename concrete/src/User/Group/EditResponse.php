<?php

namespace Concrete\Core\User\Group;

use Concrete\Core\Application\EditResponse as BaseEditResponse;

class EditResponse extends BaseEditResponse
{
    /**
     * @var \Concrete\Core\User\Group\Group[]
     */
    private $groups = [];

    /**
     * @return $this
     */
    public function setGroup(Group $group): self
    {
        $this->clearGroups();

        return $this->addGroup($group);
    }

    /**
     * @return $this
     */
    public function addGroup(Group $group): self
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * @return $this
     */
    public function setGroups(array $groups): self
    {
        $this->clearGroups();

        return $this->addGroups($groups);
    }

    /**
     * @return $this
     */
    public function addGroups(array $groups): self
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }

        return $this;
    }

    /**
     * @return \Concrete\Core\User\Group\Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @return $this
     */
    public function clearGroups(): self
    {
        $this->groups = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\EditResponse::getJSONObject()
     */
    public function getJSONObject()
    {
        $result = $this->getBaseJSONObject();
        $result->groups = $this->getGroups();

        return $result;
    }
}
