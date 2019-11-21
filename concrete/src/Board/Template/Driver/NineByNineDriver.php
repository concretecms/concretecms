<?php
namespace Concrete\Core\Board\Template\Driver;

defined('C5_EXECUTE') or die("Access Denied.");

class NineByNineDriver implements DriverInterface
{
    
    public function getTotalSlots(): int
    {
        return 9;
    }

}
