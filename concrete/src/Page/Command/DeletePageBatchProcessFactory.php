<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\User\User;

class DeletePageBatchProcessFactory implements BatchProcessFactoryInterface
{
    /**
     * @var \Concrete\Core\User\User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'delete_page';
    }

    /**
     * {@inheritdoc}
     *
     * @param \Concrete\Core\Page\Page[] $pages
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getCommands()
     */
    public function getCommands($pages): array
    {
        $commands = [];
        foreach ($pages as $page) {
            $commands[] = new DeletePageCommand($page->getCollectionID(), $this->user->getUserID());
        }

        return $commands;
    }
}
