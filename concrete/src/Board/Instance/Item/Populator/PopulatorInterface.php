<?php

namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\Item;

defined('C5_EXECUTE') or die("Access Denied.");

interface PopulatorInterface
{

    /**
     * @param Instance $instance
     * @param ConfiguredDataSource $configuredDataSource
     * @return Item[]
     */
    public function createItemsFromDataSource(
        Instance $instance,
        ConfiguredDataSource $configuredDataSource
    ): array;

    /**
     * @param $mixed
     * @return Item|null
     */
    public function createItemFromObject(DataSource $dataSource, $mixed):? Item;
}

