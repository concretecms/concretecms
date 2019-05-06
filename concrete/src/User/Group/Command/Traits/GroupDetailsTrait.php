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
     * @var int
     */
    protected $parentGroupID = 0;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }


    /**
     * @return bool
     */
    public function isBadge() : bool
    {
        return $this->isBadge;
    }

    /**
     * @param mixed $isBadge
     */
    public function setIsBadge($isBadge): void
    {
        $this->isBadge = $isBadge;
    }

    /**
     * @return string
     */
    public function getBadgeDescription() : string
    {
        return $this->badgeDescription;
    }

    /**
     * @param mixed $badgeDescription
     */
    public function setBadgeDescription($badgeDescription): void
    {
        $this->badgeDescription = $badgeDescription;
    }

    /**
     * @return mixed
     */
    public function getParentGroupID() : int
    {
        return $this->parentGroupID;
    }

    /**
     * @param mixed $parentGroupID
     */
    public function setParentGroupID($parentGroupID): void
    {
        $this->parentGroupID = $parentGroupID;
    }






}