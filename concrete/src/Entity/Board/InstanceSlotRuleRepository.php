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
        return [];
    }


}
