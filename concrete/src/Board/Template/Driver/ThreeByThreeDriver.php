<?php
namespace Concrete\Core\Board\Template\Driver;

use Concrete\Core\Board\Layout\PlannerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ThreeByThreeDriver implements DriverInterface
{

    public function getTotalSlots(): int
    {
        return 9;
    }

    public function getLayoutPlanner(): ?PlannerInterface
    {
        return null;
    }


}
