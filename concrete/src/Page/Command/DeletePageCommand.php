<?php

namespace Concrete\Core\Page\Command;

class DeletePageCommand extends PageCommand
{
    /**
     * @var int|null
     */
    protected $userID;

    public function __construct(int $pageID, ?int $userID)
    {
        parent::__construct($pageID);
        $this->setUserID($userID);
    }

    public function getUserID(): ?int
    {
        return $this->userID;
    }

    /**
     * @return $this
     */
    public function setUserID(?int $userID): object
    {
        $this->userID = $userID;

        return $this;
    }
}
