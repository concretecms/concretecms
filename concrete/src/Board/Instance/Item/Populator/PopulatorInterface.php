<?php

namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\Item;

defined('C5_EXECUTE') or die("Access Denied.");

interface PopulatorInterface
{

    const RETRIEVE_FIRST_RUN = 1;
    const RETRIEVE_NEW_ITEMS = 2;

    /**
     * @param Instance $instance
     * @param ConfiguredDataSource $configuredDataSource
     * @param int $mode
     * @return Item[]
     */
    public function createItemsFromDataSource(
        Instance $instance,
        ConfiguredDataSource $configuredDataSource,
        $mode = PopulatorInterface::RETRIEVE_FIRST_RUN
    ): array;

    /**
     * @param $mixed
     * @return Item|null
     */
    public function createItemFromObject(DataSource $dataSource, $mixed):? Item;
}

