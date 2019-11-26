<?php
namespace Concrete\Core\Board\Slot\Template\Driver;


defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{
    
    /**
     * @return int
     */
    public function getTotalContentSlots(): int;
    
}
