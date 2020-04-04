<?php
namespace Concrete\Core\Board\Instance;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\Item;
use Doctrine\ORM\EntityManager;

class ItemSegmenter
{

    /**
     * @var EntityManager 
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
        $r = $this->entityManager->getRepository(Item::class);
        $board = $instance->getBoard();
        switch($board->getSortBy()) {
            case $board::ORDER_BY_RELEVANT_DATE_ASC:
                $items = $r->findByBoard($board, ['relevantDate' => 'asc']);
                break;
            default:
                $items = $r->findByBoard($board, ['relevantDate' => 'desc']);
                break;
        }
        return $items;
    }
    

}
