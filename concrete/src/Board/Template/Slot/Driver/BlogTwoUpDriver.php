<?php
namespace Concrete\Core\Board\Template\Slot\Driver;

use Concrete\Core\Board\Instance\Slot\Content\Filterer\FiltererInterface;
use Concrete\Core\Board\Instance\Slot\Content\Filterer\SummaryObjectFilterer;

defined('C5_EXECUTE') or die("Access Denied.");

class BlogTwoUpDriver implements DriverInterface
{

    public function getTotalContentSlots(): int
    {
        return 2;
    }

    public function getSlotFilterer(): ?FiltererInterface
    {
        $filterer = new SummaryObjectFilterer();
        $filterer->registerSlot(1, [
            'blog_image_top',
        ]);
        $filterer->registerSlot(2, [
            'blog_image_top',
        ]);
        return $filterer;
    }

}
