<?php
namespace Concrete\Core\Board\Slot\Template\Driver;

defined('C5_EXECUTE') or die("Access Denied.");

class StripeDriver implements DriverInterface
{

    public function getTotalContentSlots(): int
    {
        return 1;
    }

}
