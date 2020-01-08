<?php

namespace Concrete\Core\Board\Item\Populator;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Entity\Board\ItemBatch;

defined('C5_EXECUTE') or die("Access Denied.");

interface PopulatorInterface
{

    /**
     * @param Board $board
     * @param ItemBatch $batch
     * @param ConfiguredDataSource $configuredDataSource
     * @return Item[]
     */
    public function createBoardItems(Board $board, ItemBatch $batch, ConfiguredDataSource $configuredDataSource) : array;
    
}
