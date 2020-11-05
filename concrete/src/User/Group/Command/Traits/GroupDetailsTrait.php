<?php

namespace Concrete\Core\User\Group\Command\Traits;

trait GroupDetailsTrait
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var bool
     */
    protected $isBadge = false;

    /**
     * @var string
     */
    protected $badgeDescription = '';

    /**
     * @var int|null
     */
    protected $parentGroupID;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): object
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return $this
     */
    public function setDescription(string $description): object
    {
        $this->description = $description;

        return $this;
    }

    public function isBadge(): bool
    {
        return $this->isBadge;
    }

    /**
     * @return $this
     */
    public function setIsBadge(bool $isBadge): object
    {
        $this->isBadge = $isBadge;

        return $this;
    }

    public function getBadgeDescription(): string
    {
        return $this->badgeDescription;
    }

    /**
     * @return $this
     */
    public function setBadgeDescription(string $badgeDescription): object
    {
        $this->badgeDescription = $badgeDescription;

        return $this;
    }

    public function getParentGroupID(): ?int
    {
        return $this->parentGroupID;
    }

    /**
     * @return $this
     */
    public function setParentGroupID(?int $parentGroupID): object
    {
        $this->parentGroupID = $parentGroupID;

        return $this;
    }
}
