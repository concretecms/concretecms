<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\ORM\EntityRepository;

class InstanceRepository extends EntityRepository
{

    public function findByBoardsUsingSlotTemplate(SlotTemplate $slotTemplate)
    {
        // first we determine all boards that match this form factory.
        $filteredBoards = [];
        $boards = $this->getEntityManager()->getRepository(Board::class)->findAll();
        foreach($boards as $board) {
            // Note this logic could be expensive with lots of boards. We should consider optimizing
            // this with some DQL
            /**
             * @var $board Board
             */
            if ($board->hasCustomSlotTemplates()) {
                foreach($board->getCustomSlotTemplates() as $customSlotTemplate) {
                    if ($customSlotTemplate->getFormFactor() == $slotTemplate->getFormFactor()) {
                        $filteredBoards[] = $board;
                    }
                }
            } else if ($board->getTemplate()->getDriver()->getFormFactor() == $slotTemplate->getFormFactor()) {
               $filteredBoards[] = $board;
           }
        }

        if ($filteredBoards) {
            $qb = $this->createQueryBuilder('bi');
            $instances = $qb
                ->join('bi.board', 'b')
                ->where($qb->expr()->in('b', $filteredBoards))
                ->getQuery()
                ->execute();
            return $instances;
        }
        return [];
    }


}
