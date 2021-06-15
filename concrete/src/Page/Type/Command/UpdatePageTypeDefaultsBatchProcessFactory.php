<?php

namespace Concrete\Core\Page\Type\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\Page\Page;

class UpdatePageTypeDefaultsBatchProcessFactory implements BatchProcessFactoryInterface
{
    /**
     * @var \Concrete\Core\Page\Page
     */
    protected $defaultsPage;

    public function __construct(Page $defaultsPage)
    {
        $this->defaultsPage = $defaultsPage;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'update_page_type_defaults';
    }

    /**
     * {@inheritdoc}
     *
     * @param array[] $mixed Every array item is an array with keys 'cID', 'cvID', 'blocksToUpdate', 'blocksToAdd'
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getCommands()
     */
    public function getCommands($records): array
    {
        $commands = [];
        foreach ($records as $record) {
            $commands[] = new UpdatePageTypeDefaultsCommand(
                $this->defaultsPage->getCollectionID(),
                $record['cID'],
                $record['cvID'],
                $record['blocksToUpdate'],
                $record['blocksToAdd']
            );
        }

        return $commands;
    }
}
