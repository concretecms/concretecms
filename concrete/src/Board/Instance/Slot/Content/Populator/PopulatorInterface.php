<?php

namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Board\Instance\Item\Data\DataInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface PopulatorInterface
{

    /**
     * @return string
     */
    public function getDataClass() : string;

    /**
     * @param Item $item
     * @return ObjectInterface[]
     */
    public function createContentObjects(DataInterface $data) : array;

}
