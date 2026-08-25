<?php

declare(strict_types=1);

namespace Concrete\Core\Block\Command;

use Concrete\Core\Area\Area;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

class SortAreaBlocksCommand extends Command
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @var Area
     */
    protected $area;

    /**
     * @var int[]
     */
    protected $blockIDs = [];

    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @return $this
     */
    public function setPage(Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getArea(): Area
    {
        return $this->area;
    }

    /**
     * @return $this
     */
    public function setArea(Area $area): self
    {
        $this->area = $area;

        return $this;
    }

    /**
     * The IDs of all the blocks of the area, in the wanted display order.
     *
     * @return int[]
     */
    public function getBlockIDs(): array
    {
        return $this->blockIDs;
    }

    /**
     * The IDs of all the blocks of the area, in the wanted display order.
     *
     * @param int[] $blockIDs they must be all and only the IDs of the blocks currently in the area
     *
     * @return $this
     */
    public function setBlockIDs(array $blockIDs): self
    {
        $this->blockIDs = array_values(array_map('intval', $blockIDs));

        return $this;
    }
}
