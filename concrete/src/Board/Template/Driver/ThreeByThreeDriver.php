<?php
namespace Concrete\Core\Board\Template\Driver;

defined('C5_EXECUTE') or die("Access Denied.");

class ThreeByThreeDriver implements DriverInterface
{
    
    public function getFormFactor()
    {
        return 'card';
    }

    public function getTotalSlots(): int
    {
        return 9;
    }

}
