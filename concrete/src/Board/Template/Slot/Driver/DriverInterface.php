<?php
namespace Concrete\Core\Board\Template\Slot\Driver;


use Concrete\Core\Board\Instance\Slot\Content\Filterer\FiltererInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{
    
    /**
     * @return int
     */
    public function getTotalContentSlots(): int;
    
    public function getSlotFilterer() : ?FiltererInterface;
}
