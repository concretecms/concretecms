<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Block\Block;
use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\Page\Page;

class DefaultsBlockBatchProcessFactory implements BatchProcessFactoryInterface
{
    /**
     * @var \Concrete\Core\Page\Page
     */
    protected $page;

    /**
     * @var string
     */
    protected $arHandle;

    /**
     * @var \Concrete\Core\Block\Block
     */
    protected $block;

    public function __construct(Block $block, Page $page, string $arHandle)
    {
        $this->block = $block;
        $this->page = $page;
        $this->arHandle = $arHandle;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'update_defaults';
    }

    /**
     * {@inheritdoc}
     *
     * @param array[] $blocks the blocks data. Every array item is an array with keys 'action', 'bID', 'cID', 'cvID', 'arHandle'
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getCommands()
     */
    public function getCommands($blocks): array
    {
        $commands = [];
        foreach ($blocks as $b) {
            if ($b['action'] == 'update_forked_alias') {
                $commandClass = UpdateForkedAliasDefaultsBlockCommand::class;
            } else {
                $commandClass = AddAliasDefaultsBlockCommand::class;
            }

            $command = new $commandClass(
                $this->block->getBlockID(),
                $this->page->getCollectionID(),
                $this->page->getVersionID(),
                $this->arHandle,
                $b['bID'],
                $b['cID'],
                $b['cvID'],
                $b['arHandle']
            );
            $commands[] = $command;
        }

        return $commands;
    }
}
