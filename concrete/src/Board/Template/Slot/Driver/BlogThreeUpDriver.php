<?php
namespace Concrete\Core\Board\Template\Slot\Driver;

use Concrete\Core\Board\Instance\Slot\Content\Filterer\FiltererInterface;
use Concrete\Core\Board\Instance\Slot\Content\Filterer\SummaryObjectFilterer;

defined('C5_EXECUTE') or die("Access Denied.");

class BlogThreeUpDriver implements DriverInterface
{

    public function getTotalContentSlots(): int
    {
        return 3;
    }

    public function getSlotFilterer(): ?FiltererInterface
    {
        $filterer = new SummaryObjectFilterer();
        $filterer->registerSlot(1, [
            'blog_entry_card',
        ]);
        $filterer->registerSlot(2, [
            'blog_entry_card',
        ]);
        $filterer->registerSlot(3, [
            'blog_entry_card',
        ]);
        return $filterer;
    }

}
