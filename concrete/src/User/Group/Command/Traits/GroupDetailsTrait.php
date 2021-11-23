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

    /**
     * @var int|null
     */
    protected $parentNodeID;

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

    /**
     * @deprecated
     * @return bool
     */
    public function isBadge(): bool
    {
        return $this->isBadge;
    }

    /**
     * @deprecated
     * @return $this
     */
    public function setIsBadge(bool $isBadge): object
    {
        $this->isBadge = $isBadge;

        return $this;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getBadgeDescription(): string
    {
        return $this->badgeDescription;
    }

    /**
     * @deprecated
     * @return $this
     */
    public function setBadgeDescription(string $badgeDescription): object
    {
        $this->badgeDescription = $badgeDescription;

        return $this;
    }

    /**
     * @deprecated
     * Use the parent node instead.
     *
     * @return int|null
     */
    public function getParentGroupID(): ?int
    {
        return $this->parentGroupID;
    }

    /**
     * @deprecated
     * Use the parent node instead.
     *
     * @return $this
     */
    public function setParentGroupID(?int $parentGroupID): object
    {
        $this->parentGroupID = $parentGroupID;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getParentNodeID(): ?int
    {
        return $this->parentNodeID;
    }

    /**
     * @param int|null $parentNodeID
     * @return object
     */
    public function setParentNodeID(?int $parentNodeID): object
    {
        $this->parentNodeID = $parentNodeID;
        return $this;
    }

}
