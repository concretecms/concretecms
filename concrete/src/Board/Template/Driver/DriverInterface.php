<?php
namespace Concrete\Core\Board\Template\Driver;


defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{
    
    public function getTotalSlots() : int;

    
}
