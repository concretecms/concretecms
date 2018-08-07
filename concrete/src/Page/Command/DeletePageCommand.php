<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class DeletePageCommand extends PageCommand implements BatchableCommandInterface
{

    protected $userID;

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param mixed $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    public function __construct($pageID, $userID)
    {
        $this->userID = $userID;
        parent::__construct($pageID);
    }

    public function getBatchHandle()
    {
        return 'delete_page';
    }

}