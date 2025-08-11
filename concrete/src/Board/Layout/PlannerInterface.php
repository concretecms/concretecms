<?php

namespace Concrete\Core\Board\Layout;

use Concrete\Core\Board\Instance\Slot\Planner\PlannedInstance;
use Concrete\Core\Board\Instance\Slot\Planner\PlannedSlot;
use Concrete\Core\Entity\Board\SlotTemplate;

defined('C5_EXECUTE') or die("Access Denied.");

interface PlannerInterface
{

    public function isValidTemplate(SlotTemplate $template, PlannedInstance $plannedInstance, int $slot): bool;

    public function isValidPlannedSlot(
        PlannedSlot $plannedSlotTemplate,
        PlannedInstance $plannedInstance,
        int $slot
    ): bool;

    public function isValidInstance(PlannedInstance $plannedInstance): bool;

}
