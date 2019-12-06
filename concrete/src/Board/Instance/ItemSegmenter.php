<?php
namespace Concrete\Core\Board\Instance;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlot;
use Doctrine\Common\Collections\ArrayCollection;

class ItemSegmenter
{

    /**
     * Responsible for Getting board data items and returning them. This may involve taking a sub-set of all
     * data objects, for example, or it may involve complex weighting. Used by create board instance commands
     * and other commands that populate content into boards.
     *
     * @TODO - implement complex weighting and subset building ;-)
     *
     * @param $instance Instance
     * @return Item[]
     */
    public function getBoardItemsForInstance(Instance $instance)
    {
        $items = $instance->getBoard()->getItems()->toArray();
        return $items;
    }
    

}
