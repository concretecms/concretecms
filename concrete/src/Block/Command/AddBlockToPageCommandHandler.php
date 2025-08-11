<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Events\BlockAdd;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Stack\Stack;

class AddBlockToPageCommandHandler
{

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(AddBlockToPageCommand $command)
    {
        $page = $command->getPage();
        $area = $command->getArea();
        $blockType = $command->getBlockType();
        $data = $command->getData();

        $pageToModify = $page;
        $areaToModify = $area;
        if ($area->isGlobalArea()) {
            $pageToModify = Stack::getByName($area->getAreaHandle());
            $areaToModify = Area::get($pageToModify, STACKS_AREA_NAME);
        }

        if (!$blockType->includeAll()) {
            $nvc = $pageToModify->getVersionToModify();
            $nb = $nvc->addBlock($blockType, $areaToModify, $data);
        } else {
            // if we apply to all, then we don't worry about a new version of the page
            $nb = $pageToModify->addBlock($blockType, $areaToModify, $data);
        }

        $event = new BlockAdd($nb, $pageToModify);
        $this->dispatcher->dispatch('on_block_add', $event);

        if ($area->isGlobalArea() && $nvc instanceof Collection) {
            $xvc = $page->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }

        return $nb;
    }


}
