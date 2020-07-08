<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\ORM\EntityRepository;

class InstanceSlotRuleRepository extends EntityRepository
{

    public function findByMultipleInstances(array $instances)
    {
        if (count($instances)) {
            $rules = $instances[0]->getRules()->toArray();
            if (count($instances) > 1) {
                foreach($instances as $instance) {
                    $instanceRuleBatchIds = [];
                    foreach($instance->getRules() as $instanceRule) {
                        $instanceRuleBatchIds[] = $instanceRule->getBatchIdentifier();
                    }
                    foreach($rules as $i => $rule) {
                        if (!in_array($rule->getBatchIdentifier(), $instanceRuleBatchIds)) {
                            unset($rules[$i]);
                        }
                    }
                }
            }
            return $rules;
        }
        // first we determine all boards that match this form factory.
        /*
        $filteredBoards = [];
        $boards = $this->getEntityManager()->getRepository(Board::class)->findAll();
        foreach($boards as $board) {
            // Note this logic could be expensive with lots of boards. We should consider optimizing
            // this with some DQL
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
        */
        return [];
    }


}
