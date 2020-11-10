<?php
namespace Concrete\Core\Board\Template\Driver;

use Concrete\Core\Board\Layout\PlannerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{

    /**
     * Return a single string if the form factor is the same for all slots. Otherwise return an array in the format
     * 'Slot Name' => 'Form Factor'
     * @return string|array
     */
    public function getFormFactor();

    /**
     * @return int
     */
    public function getTotalSlots(): int;


    public function getLayoutPlanner(): ?PlannerInterface;


}
