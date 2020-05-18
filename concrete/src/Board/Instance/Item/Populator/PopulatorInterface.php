<?php

namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\InstanceItemBatch;

defined('C5_EXECUTE') or die("Access Denied.");

interface PopulatorInterface
{

    /**
     * @param Instance $instance
     * @param InstanceItemBatch $batch
     * @param ConfiguredDataSource $configuredDataSource
     * @return InstanceItem[]
     */
    public function createBoardInstanceItems(
        Instance $instance,
        InstanceItemBatch $batch,
        ConfiguredDataSource $configuredDataSource,
        int $since = 0
    ): array;

}
