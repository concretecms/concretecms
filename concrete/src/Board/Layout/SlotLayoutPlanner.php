<?php

namespace Concrete\Core\Board\Layout;

use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Board\Instance\Slot\Planner\PlannedInstance;
use Concrete\Core\Entity\Board\SlotTemplate;

defined('C5_EXECUTE') or die("Access Denied.");

class SlotLayoutPlanner implements PlannerInterface
{

    protected $registry = [];

    /**
     * SlotLayoutPlanner constructor.
     * @param array $registry
     */
    public function __construct(array $registry)
    {
        $this->registry = $registry;
    }


    public function isValidInstance(PlannedInstance $plannedInstance): bool
    {
        return true;
    }

    public function isValidTemplate(SlotTemplate $template, PlannedInstance $plannedInstance, int $slot): bool
    {
        if (isset($this->registry[$slot])) {
            if (!in_array($template->getHandle(), $this->registry[$slot])) {
                return false;
            }
        }
        return true;
    }


}
