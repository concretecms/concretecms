<?php
namespace Concrete\Core\Board\Instance\Slot\Content\Filterer;

use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface FiltererInterface
{

    /**
     * @param ObjectInterface[] $objects
     * @param int $slot
     * @return ObjectInterface[]
     */
    public function findContentObjectsForSlot(array $objects, int $slot) : array;

}
