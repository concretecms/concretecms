<?php

namespace Concrete\Core\Page\Type\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;

class UpdatePageTypeDefaultsBatchProcessFactory implements BatchProcessFactoryInterface
{

    protected $defaultsPage;

    public function __construct($defaultsPage)
    {
        $this->defaultsPage = $defaultsPage;
    }

    public function getBatchHandle()
    {
        return 'update_page_type_defaults';
    }

    public function getCommands($mixed): array
    {
        $commands = [];
        foreach ($mixed as $record) {
            $commands[] = new UpdatePageTypeDefaultsCommand(
                $this->defaultsPage->getCollectionID(),
                $record['cID'], $record['cvID'], $record['blocksToUpdate'],
                $record['blocksToAdd']
            );
        }
        return $commands;
    }


}