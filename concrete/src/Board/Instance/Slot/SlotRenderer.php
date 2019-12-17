<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Entity\Board\Instance;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;

class SlotRenderer
{

    /**
     * @var Instance
     */
    protected $instance;

    /**
     * @var PersistentCollection 
     */
    protected $slots;
    
    /**
     * SlotRenderer constructor.
     * @param Instance $instance
     */
    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
        $this->slots = $instance->getSlots();
    }
    
    public function display(int $slot)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('slot', $slot));
        $matched = $this->slots->matching($criteria)[0];
        if ($matched) {
            $block = Block::getByID($matched->getBlockID());
            if ($block) {
                $view = new BlockView($block);
                $view->setBlockViewHeaderFile(DIR_FILES_ELEMENTS_CORE . '/boards/slot_header.php');
                $view->setBlockViewFooterFile(DIR_FILES_ELEMENTS_CORE . '/boards/slot_footer.php');
                $view->render();
            }
        }

    }


}

