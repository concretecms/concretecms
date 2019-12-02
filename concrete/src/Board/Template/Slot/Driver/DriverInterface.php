<?php
namespace Concrete\Core\Board\Template\Slot\Driver;


defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{
    
    /**
     * @return int
     */
    public function getTotalContentSlots(): int;
    
}
