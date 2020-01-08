<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use Concrete\Core\User\User;

class DeletePageBatchProcessFactory implements BatchProcessFactoryInterface
{

    /**
     * @var User
     */
    protected $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getBatchHandle()
    {
        return 'delete_page';
    }

    public function getCommands($pages) : array
    {
        $commands = [];
        foreach ($pages as $page) {
            $commands[] = new DeletePageCommand($page->getCollectionID(), $this->user->getUserID());
        }
        return $commands;
    }
}