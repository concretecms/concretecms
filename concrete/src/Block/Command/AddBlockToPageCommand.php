<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Page\Page;

class AddBlockToPageCommand extends Command
{

    /**
     * @var BlockType
     */
    protected $blockType;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var Area
     */
    protected $area;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var \Concrete\Core\Block\Block|null
     */
    protected $beforeBlock;

    /**
     * @var string
     */
    protected $saveMode = SaveMode::SAVE_MODE_REQUEST;

    /**
     * @return BlockType
     */
    public function getBlockType(): BlockType
    {
        return $this->blockType;
    }

    /**
     * @param BlockType $blockType
     */
    public function setBlockType(BlockType $blockType): void
    {
        $this->blockType = $blockType;
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

    /**
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }

    /**
     * @param Area $area
     */
    public function setArea(Area $area): void
    {
        $this->area = $area;
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
     * The already existing block that the new block should be placed before.
     *
     * @return Block|null NULL if the new block should be added at the end of the area
     */
    public function getBeforeBlock(): ?Block
    {
        return $this->beforeBlock;
    }

    /**
     * The already existing block that the new block should be placed before.
     *
     * @param \Concrete\Core\Block\Block|null $beforeBlock NULL if the new block should be added at the end of the area
     *
     * @return $this
     */
    public function setBeforeBlock(?Block $beforeBlock): self
    {
        $this->beforeBlock = $beforeBlock;

        return $this;
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


    
}
