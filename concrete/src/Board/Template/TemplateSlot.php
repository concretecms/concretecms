<?php
namespace Concrete\Core\Board\Template;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Board\Template\TemplateInstance;

class TemplateSlot
{
    
    /**
     * @var TemplateInstance 
     */
    protected $instance;

    /**
     * @var int 
     */
    protected $slot;

   
    public function __construct(TemplateInstance $instance, int $slot)
    {
        $this->instance = $instance;
        $this->slot = $slot;
    }

    public function display()
    {
        $collection = $this->instance->getCollection();
        if ($item = $collection->get($this->slot)) {
            $bID = $item->getBlockID();
            if ($bID) {
                $block = Block::getByID($bID);
                if ($block) {
                    $view = new BlockView($block);
                    $view->render();
                }
            }
        }
    }
}
