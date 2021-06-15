<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class DeletePageCommand extends PageCommand implements BatchableCommandInterface
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'delete_page';
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
