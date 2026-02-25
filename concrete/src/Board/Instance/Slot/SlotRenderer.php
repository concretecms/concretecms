<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Board\Instance\Slot\Menu\Manager;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Application\Application;

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

    /**
     * @var bool
     */
    protected $enableEditing = false;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param bool $enableEditing
     */
    public function setEnableEditing($enableEditing)
    {
        $this->enableEditing = $enableEditing;
    }

    public function __construct(Application $app, FileLocator $fileLocator, RenderedSlotCollection $renderedSlotCollection)
    {
        $this->app = $app;
        $this->fileLocator = $fileLocator;
        $this->renderedSlotCollection = $renderedSlotCollection;
    }

    /**
     * Returns true if the slot has a block inside it.
     *
     * @param int $slot
     */
    public function hasContents(int $slot)
    {
        $slot = $this->renderedSlotCollection->getRenderedSlot($slot);
        if ($slot) {
            return $slot->getBlockID() > 0;
        }
        return false;
    }

    public function display(int $slot)
    {
        $slot = $this->renderedSlotCollection->getRenderedSlot($slot);
        if ($slot) {
            $block = Block::getByID($slot->getBlockID());
            if ($this->enableEditing) {
                $menuManager = $this->app->make(Manager::class);
                $menu = $menuManager->getMenu($slot);
                include $this->fileLocator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_BOARDS . '/slot_header.php')
                    ->getFile();

            }
            if ($block) {
                $view = new BlockView($block);
                $view->render();
            }
            if ($this->enableEditing) {
                include $this->fileLocator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_BOARDS . '/slot_footer.php')
                    ->getFile();
            }
        }

    }


}

