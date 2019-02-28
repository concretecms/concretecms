<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Block\Block;
use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\Page\Page;

class DefaultsBlockBatchProcessFactory implements BatchProcessFactoryInterface
{

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var string
     */
    protected $arHandle;

    /**
     * @var Block
     */
    protected $block;

    public function __construct(Block $block, Page $page, $arHandle)
    {
        $this->block = $block;
        $this->page = $page;
        $this->arHandle = $arHandle;
    }

    public function getBatchHandle()
    {
        return 'update_defaults';
    }

    public function getCommands($blocks): array
    {
        $commands = [];
        foreach ($blocks as $b) {
            if ($b['action'] == 'update_forked_alias') {
                $command = UpdateForkedAliasDefaultsBlockCommand::class;
            } else {
                $command = AddAliasDefaultsBlockCommand::class;
            }

            $command = new $command(
                $this->block->getBlockID(), $this->page->getCollectionID(),
                $this->page->getVersionID(), $this->arHandle,
                $b['bID'], $b['cID'], $b['cvID'], $b['arHandle']
            );
            $commands[] = $command;
        }
        return $commands;
    }


}