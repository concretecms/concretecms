<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;

defined('C5_EXECUTE') or die('Access Denied.');

interface ItemInterface
{
    public function hasItems(Package $package);

    public function getItems(Package $package);

    public function getItemName($mixed);

    public function getItemCategoryDisplayName();

    public function removeItems(Package $package);

    public function renderList(Package $package);
}
