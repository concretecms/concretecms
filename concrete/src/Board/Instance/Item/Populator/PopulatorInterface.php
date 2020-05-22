<?php

namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\InstanceItemBatch;

defined('C5_EXECUTE') or die("Access Denied.");

interface PopulatorInterface
{

    const RETRIEVE_FIRST_RUN = 1;
    const RETRIEVE_NEW_ITEMS = 2;

    /**
     * @param Instance $instance
     * @param InstanceItemBatch $batch
     * @param ConfiguredDataSource $configuredDataSource
     * @param int $mode
     * @return array
     */
    public function createBoardInstanceItems(
        Instance $instance,
        InstanceItemBatch $batch,
        ConfiguredDataSource $configuredDataSource,
        $mode = PopulatorInterface::RETRIEVE_FIRST_RUN
    ): array;

}
