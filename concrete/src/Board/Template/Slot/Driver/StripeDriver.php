<?php
namespace Concrete\Core\Board\Template\Slot\Driver;

use Concrete\Core\Board\Instance\Slot\Content\Filterer\FiltererInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class StripeDriver implements DriverInterface
{

    public function getTotalContentSlots(): int
    {
        return 1;
    }
    
    public function getSlotFilterer(): ?FiltererInterface
    {
        return null;
    }

}
