<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Foundation\Command\Command;

class DeleteBoardInstanceSlotRuleCommand extends Command
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
