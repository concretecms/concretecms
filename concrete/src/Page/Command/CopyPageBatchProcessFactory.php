<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use Concrete\Core\Page\Page;

class CopyPageBatchProcessFactory implements BatchProcessFactoryInterface
{

    /**
     * @var Page
     */
    protected $destination;

    /**
     * @var bool
     */
    protected $isMultilingual;

    public function __construct(Page $destination, $isMultilingual)
    {
        $this->destination = $destination;
        $this->isMultilingual = $isMultilingual;
    }

    public function getBatchHandle()
    {
        return 'copy_page';
    }

    public function getCommands($pages) : array
    {
        $commands = [];
        foreach ($pages as $cID) {
            $commands[] = new CopyPageCommand($cID, $this->destination->getCollectionID(), $this->isMultilingual);
        }
        return $commands;
    }
}