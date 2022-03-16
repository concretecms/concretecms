<?php
namespace Concrete\Core\Board\Template\Driver;

use Concrete\Core\Board\Layout\PlannerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{

    /**
     * @return int
     */
    public function getTotalSlots(): int;


    public function getLayoutPlanner(): ?PlannerInterface;


}
