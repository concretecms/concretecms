<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\Page\Page;

class CopyPageBatchProcessFactory implements BatchProcessFactoryInterface
{
    /**
     * @var \Concrete\Core\Page\Page
     */
    protected $destination;

    /**
     * @var bool
     */
    protected $isMultilingual;

    public function __construct(Page $destination, bool $isMultilingual)
    {
        $this->destination = $destination;
        $this->isMultilingual = $isMultilingual;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'copy_page';
    }

    /**
     * {@inheritdoc}
     *
     * @param int[] $pages
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getCommands()
     */
    public function getCommands($pages): array
    {
        $commands = [];
        foreach ($pages as $cID) {
            $commands[] = new CopyPageCommand($cID, $this->destination->getCollectionID(), $this->isMultilingual);
        }

        return $commands;
    }
}
