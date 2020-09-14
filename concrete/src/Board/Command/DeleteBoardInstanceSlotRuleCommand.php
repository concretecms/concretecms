<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;

class DeleteBoardInstanceSlotRuleCommand
{

    /**
     * @var InstanceSlotRule
     */
    protected $rule;

    /**
     * DeleteBoardInstanceRuleCommand constructor.
     * @param InstanceSlotRule $rule
     */
    public function __construct(InstanceSlotRule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * @return InstanceSlotRule
     */
    public function getRule(): InstanceSlotRule
    {
        return $this->rule;
    }



}
