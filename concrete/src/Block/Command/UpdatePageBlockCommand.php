<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Page\Page;

class UpdatePageBlockCommand extends Command
{

    /**
     * @var Block
     */
    protected $block;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $saveMode = SaveMode::SAVE_MODE_REQUEST;

    /**
     * @return Block
     */
    public function getBlock(): Block
    {
        return $this->block;
    }

    /**
     * @param Block $block
     */
    public function setBlock(Block $block): void
    {
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * How should the block controller save() method interpret the data?
     *
     * @see \Concrete\Core\Block\Controller\SaveMode
     */
    public function getSaveMode(): string
    {
        return $this->saveMode;
    }

    /**
     * How should the block controller save() method interpret the data?
     *
     * @see \Concrete\Core\Block\Controller\SaveMode
     */
    public function setSaveMode(string $saveMode): void
    {
        $this->saveMode = $saveMode;
    }


    /**
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage(Page $page): void
    {
        $this->page = $page;
    }





    
}
