<?php
namespace Concrete\Core\Board\Template\Slot\Driver;

defined('C5_EXECUTE') or die("Access Denied.");

class CardDriver implements DriverInterface
{

    public function getTotalContentSlots(): int
    {
        return 1;
    }

}
