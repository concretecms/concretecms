<?php
namespace Concrete\Core\Board\Template\Slot\Driver;

use Concrete\Core\Board\Instance\Slot\Content\Filterer\FiltererInterface;
use Concrete\Core\Board\Instance\Slot\Content\Filterer\SummaryObjectFilterer;

defined('C5_EXECUTE') or die("Access Denied.");

class BlogImageLeftDriver implements DriverInterface
{

    public function getTotalContentSlots(): int
    {
        return 1;
    }

    public function getSlotFilterer(): ?FiltererInterface
    {
        $filterer = new SummaryObjectFilterer();
        $filterer->registerSlot(1, [
            'blog_image_left',
        ]);
        return $filterer;
    }

}
