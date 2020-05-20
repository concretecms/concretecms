<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Board\Instance\Slot\Menu\Manager;
use Concrete\Core\Filesystem\FileLocator;

class SlotRenderer
{

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var RenderedSlotCollection
     */
    protected $renderedSlotCollection;

    public function __construct(FileLocator $fileLocator, RenderedSlotCollection $renderedSlotCollection)
    {
        $this->fileLocator = $fileLocator;
        $this->renderedSlotCollection = $renderedSlotCollection;
    }

    public function display(int $slot)
    {
        $slot = $this->renderedSlotCollection->getRenderedSlot($slot);
        if ($slot) {
            $block = Block::getByID($slot->getBlockID());
            if ($block) {
                $menuManager = app()->make(Manager::class);
                $menu = $menuManager->getMenu($slot);
                include $this->fileLocator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_BOARDS . '/slot_header.php')
                    ->getFile();
                $view = new BlockView($block);
                $view->render();
                include $this->fileLocator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_BOARDS . '/slot_footer.php')
                    ->getFile();
            }
        }

    }


}

